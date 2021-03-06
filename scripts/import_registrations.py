#
# MySQL Source:      entrant 
# Mongo Destination: registrations
#

import pymongo
from bson.objectid import ObjectId
from decimal import Decimal
import MySQLdb as mdb
import pprint, sys, json, os
from mongo_tools import getMongoId

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
user_metadata_cache = {}
user_metadata_fields = ['firstname', 'middlename', 'lastname', 'height', 'weight', 'birthdate', 'gender']

while mysql_record != None:
    new_reg  = {'mysql_id': int(mysql_record['id'])}
    user_doc = None

    # Link to a user document
    if 'c_id' in mysql_record:
        val = int(mysql_record['c_id'])

        user_doc_id = getMongoId(users_coll, val, user_cache, 'mysql_ids')

        if user_doc_id != None:
            new_reg['user_id'] = user_doc_id

        if str(user_doc_id) in user_metadata_cache:
            user_meta = user_metadata_cache[str(user_doc_id)]
        else:
            user_meta = users_coll.find_one({'_id' : user_doc_id}, user_metadata_fields)
            if user_meta:
                del user_meta['_id']
                user_metadata_cache[str(user_doc_id)] = user_meta
                    
        if user_meta:
            new_reg['user_data'] = user_meta

    if mysql_record['paid']:
        if mysql_record['paid'] == 'Y':
            new_reg['paid'] = True
            new_reg['status'] = 'active'
        else:
            new_reg['paid'] = False
            new_reg['status'] = 'pending'


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

        if f == 'sortsex':
            if val == 'M':
                new_reg['gender'] = 'male'
            else 
                new_reg['gender'] = 'female'

        if f in name_changes:
            new_f = name_changes[f]
            new_reg[new_f] = val
            continue

        if f == 'league_id':
            league_doc_id = getMongoId(leagues_coll, val, league_cache)

            if league_doc_id != None:
                new_reg['league_id'] = league_doc_id
            

        if f == 'rank':
            if 'secondary_rank_data' not in new_reg:
                new_reg['secondary_rank_data'] = {}

            new_reg['secondary_rank_data']['commish_rank'] = float(val)

        if f[:9] == 'volunteer':
            user_doc[f] = val
            user_doc.save()

        if f == 'pair':
            new_reg['pair'] = {'text':val}

        if f == 'afdc_virgin' and (val == '1' or val == 'Y'):
            new_reg['first_league'] = True

        if f == 'waitlist' and val == 'Y':
            new_reg['waitlist'] = True
            new_reg['waitlist_priority'] = 0

        if f in ['availability', 'attend_mst', 'attend_eos'] and 'availability' not in new_reg:
            new_reg['availability'] = {}

        if f == 'availability':
            new_reg['availability']['general'] = val + '%'

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

            if f == 'srank':
                f = 'self_rank'

            new_reg['secondary_rank_data'][f] = val

    if 'gender' not in new_reg and 'user_data' in new_reg:
        if 'gender' in new_reg['user_data']:
            new_reg['gender'] = new_reg['user_data']['gender']

    reg_coll.save(new_reg)

    write_count += 1
    if write_count % 25 == 0:
        sys.stdout.write('#')
    if write_count % (25*25) == 0:
        print

    mysql_record = old_cur.fetchone()

print