#
# MySQL Source:      entrant 
# Mongo Destination: registrations
#

import pymongo
from bson.objectid import ObjectId
from decimal import Decimal
import MySQLdb as mdb
import pprint, sys, json, os

config_json = open('../config/config.json')
config      = json.load(config_json)
config_json.close()

mysql_config = config['mysql']
mongo_config = config['mongo']

identical_fields = [
    'notes'
]

name_changes = {
    'registration_date': 'signup_timestamp',
    'playertype':        'player_strength'
}

# Load MySQL
old_con = mdb.connect(mysql_config['server'], mysql_config['username'], mysql_config['password'], mysql_config['database'])
old_cur = old_con.cursor(mdb.cursors.DictCursor)
old_cur.execute("SELECT * FROM entrant ORDER BY c_id")
mysql_record = old_cur.fetchone()

new_con      = pymongo.Connection()
platypus_db  = new_con[mongo_config['database']]
reg_coll     = platypus_db['registrations']
users_coll   = platypus_db['users']
leagues_coll = platypus_db['leagues']

# Clear Existing:
reg_coll.remove()

write_count = 0

# Cache document IDs based on mysql id to speed up the import, no need to query for these multiple times
user_cache   = {}
league_cache = {}

while mysql_record != None:
    new_reg  = {'mysql_id': int(mysql_record['id'])}
    user_doc = None

    # Link to a user document
    if 'c_id' in mysql_record:
        val = int(mysql_record['c_id'])
        if val not in user_cache:
            user_doc = users_coll.find_one({'mysql_ids': val})
            if user_doc != None:
                user_doc_id = user_doc['_id']
            else:
                user_doc_id = None
                
            user_cache[val] = user_doc_id
        else:
            user_doc_id = user_cache[val]

        if user_doc_id != None:
            new_reg['user_id'] = user_doc_id

    # Strip nulls and empty strings, convert data:
    for f in mysql_record.keys():
        val = mysql_record[f]

        if type(val) is long:
            val = int(val)

        if type(val) is Decimal:
            val = float(val)

        # Strip Nulls and Blanks
        if val == None or val == '':
            del mysql_record[f]
            continue

        # Do the conversion
        if f in identical_fields:
            new_reg[f] = val
            continue

        if f in name_changes:
            new_f = name_changes[f]
            new_reg[new_f] = val
            continue

        if f == 'league_id':
            if val not in league_cache:
                league_doc = leagues_coll.find_one({'mysql_id': val})
                if league_doc != None:
                    league_doc_id = league_doc['_id']
                else:
                    league_doc_id = None

                league_cache[val] = league_doc_id
            else:
                league_doc_id = league_cache[val]

            if league_doc_id != None:
                new_reg['league_id'] = league_doc_id
            

        if f == 'rank':
            new_reg['official_rank'] = float(val)

        if f[:9] == 'volunteer':
            user_doc[f] = val
            user_doc.save()

        if f == 'pair':
            new_reg['pair'] = {'text':val}

        if f == 'afdc_virgin' and (val == '1' or val == 'Y'):
            new_reg['first_league'] = True

        if f == 'paid' and val == 'Y':
            new_reg['paid'] = True

        if f == 'waitlist' and val == 'Y':
            new_reg['waitlist'] = True
            new_reg['waitlist_priority'] = 0

        if f in ['availability', 'attend_mst', 'attend_eos'] and 'availability' not in new_reg:
            new_reg['availability'] = {}

        if f == 'availability':
            new_reg['availability']['general'] = val

        if f == 'attend_mst' and val == '1':
            new_reg['availability']['attend_tourney_mst'] = True

        if f == 'attend_eos' and val == '1':
            new_reg['availability']['attend_tourney_eos'] = True

        if f[:6] == 'wants_' and val == '1':
            new_f = f[6:]
            if 'team_style_pref' not in new_reg:
                new_reg['team_style_pref'] = {}
            
            new_reg['team_style_pref'][new_f] = True

        if f in ['grank', 'srank', 'rank_notes']:
            if 'secondary_rank_data' not in new_reg:
                new_reg['secondary_rank_data'] = {}

            if f == 'rank_notes':
                f = 'notes'

            new_reg['secondary_rank_data'][f] = val

    reg_coll.save(new_reg)

    write_count += 1
    if write_count % 25 == 0:
        sys.stdout.write('#')
    if write_count % (25*25) == 0:
        print

    mysql_record = old_cur.fetchone()

print