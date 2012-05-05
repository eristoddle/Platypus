#
# MySQL Source:      leagues, registration 
# Mongo Destination: leagues
#

import pymongo
from bson.objectid import ObjectId
import MySQLdb as mdb
import pprint, sys, json, os
from mongo_tools import getMongoId

config_json = open('../config/config.json')
config      = json.load(config_json)
config_json.close()

mysql_config = config['mysql']
mongo_config = config['mongo']

identical_fields = [
    'name', 'forefeit_score', 'season'
]

name_changes = {
    'league_start': 'start_date',
    'league_end':   'end_date',
    'type':         'sport'
}

# Load MySQL
old_con = mdb.connect(mysql_config['server'], mysql_config['username'], mysql_config['password'], mysql_config['database'])
old_cur = old_con.cursor(mdb.cursors.DictCursor)
old_cur.execute("SELECT * FROM leagues ORDER BY id")
mysql_record = old_cur.fetchone()

new_con     = pymongo.Connection()
platypus_db = new_con[mongo_config['database']]
league_coll = platypus_db['leagues']
user_coll   = platypus_db['users']

# Clear Existing:
league_coll.remove()

#Setup Caching
user_cache = {}

#Main Loop for Leagues table.
while mysql_record != None:
    mysql_id = int(mysql_record['id'])

    new_league = {}
    new_league['mysql_id'] = mysql_id

    # Strip nulls and empty strings, convert data:
    for f in mysql_record.keys():
        val = mysql_record[f]

        if type(val) is long:
            val = int(val)

        # Strip Nulls and Blanks
        if val == None or val == '':
            del mysql_record[f]
            continue

        # Do the conversion
        if f in identical_fields:
            new_league[f] = val
            continue

        if f in name_changes:
            new_f = name_changes[f]
            new_league[new_f] = val
            continue

        if f == 'commish':
            commish_doc_id = getMongoId(user_coll, int(val), user_cache, 'mysql_ids')

            if commish_doc_id != None:
                new_league['commissioner_ids'] = [commish_doc_id]

        if f == 'max_men':
            new_league['player_limit'] = {'male': int(val)}

        if f == 'juniors':
            division_list = ['adult', 'juniors']
            new_league['age_division'] = division_list[val]


    print "Saving " + mysql_record['name']
    new_id = league_coll.save(new_league)
    mysql_record = old_cur.fetchone()


# Import fields from registrations
old_cur.execute("SELECT * FROM registration ORDER BY id")
mysql_record = old_cur.fetchone()

while mysql_record != None:
    if mysql_record['league2'] != None or mysql_record['league3'] != None:
        mysql_record = old_cur.fetchone()
        continue

    changeset = { '$set': {
        'price': mysql_record['cost'],
        'registration_open': mysql_record['reg_start'],
        'registration_close': mysql_record['reg_end']
    }}

    conditions = {
        'mysql_id': mysql_record['league1']
    }

    league_coll.update(conditions, changeset)
    mysql_record = old_cur.fetchone()

