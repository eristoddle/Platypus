#
# Source:      field        MySQL
# Destination: fieldsites   Mongo
#

import pymongo
from bson.objectid import ObjectId
import MySQLdb as mdb
import pprint, sys, json, os

config_json = open('../config/config.json')
config      = json.load(config_json)
config_json.close()

mysql_config = config['mysql']
mongo_config = config['mongo']

identical_fields = [
    'directions'
]

name_changes = {
    'desc': 'name',
    'map_link': 'map_url',
    'field_id': 'mysql_id'
}

# Load MySQL
old_con = mdb.connect(mysql_config['server'], mysql_config['username'], mysql_config['password'], mysql_config['database'])
old_cur = old_con.cursor(mdb.cursors.DictCursor)
old_cur.execute("SELECT * FROM field ORDER BY field_id")
mysql_record = old_cur.fetchone()

new_con     = pymongo.Connection()
platypus_db = new_con[mongo_config['database']]
fields_coll  = platypus_db['fieldsites']

# Clear Existing:
fields_coll.remove()

# This is where the magic happens
while mysql_record != None:
    new_field = {}

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
            new_field[f] = val
            continue

        if f in name_changes:
            new_f = name_changes[f]
            new_field[new_f] = val
            continue

        if f == 'current':
            new_field['active'] = True if val == 'Y' else False
    try:
        print "Saving " + mysql_record['desc']
        new_id = fields_coll.save(new_field)
    except Exception:
        pprint.pprint(mysql_record)
        sys.exit()
    mysql_record = old_cur.fetchone()
