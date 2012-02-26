import pymongo
from bson.objectid import ObjectId
import MySQLdb as mdb
import pprint, sys, json, os

config_json = open('../config/config.json')
config      = json.load(config_json)
config_json.close()

mysql_config = config['mysql']
mongo_config = config['mongo']
image_folder = '../webroot/img/users/';

identical_fields = [
    "firstname", "middlename", "lastname", "city", "state",
    "hometown", "occupation", "college", "alias", "handedness"
]

name_changes = {
    "email":     "email_address",
    "sex":       "gender",
    "zip":       "postal_code",
    "upa":       "usau_id"
}

# Load MySQL
old_con = mdb.connect(mysql_config['server'], mysql_config['username'], mysql_config['password'], mysql_config['database'])
old_cur = old_con.cursor(mdb.cursors.DictCursor)
old_cur.execute("SELECT * FROM contact ORDER BY c_id")
mysql_record = old_cur.fetchone()

new_con     = pymongo.Connection()
platypus_db = new_con[mongo_config['database']]
users_coll  = platypus_db['users']
ident_coll  = platypus_db['identities']

# Clear Existing:
users_coll.remove()
ident_coll.remove()

# Erase images from previous import
for the_file in os.listdir(image_folder):
    file_path = os.path.join(image_folder, the_file)
    try:
        if os.path.isfile(file_path):
            os.unlink(file_path)
    except Exception, e:
        print e

# This is where the magic happens
while mysql_record != None:
    mysql_id       = mysql_record['c_id']
    photo_data     = None
    new_user       = None
    old_identity   = None
    new_address    = None

    if mysql_record['username'] != None and mysql_record['username'] != '':
        forum_username = mysql_record['username'].lower()
    else:
        forum_username = None

    # check for existing mongo user
    if forum_username != None:
        old_identity = ident_coll.find_one({"type": "phpbb", "prv_uid": forum_username});
        if old_identity != None:
            new_user = users_coll.find_one({"_id": old_identity["user_id"]})

    # Instantiate a blank user document
    if new_user == None:
        new_user = {}
    else:
        print "Duplicate found: " + forum_username

    if 'mysql_ids' in new_user:
        new_user['mysql_ids'].append(mysql_id)
    else:
        new_user['mysql_ids'] = [mysql_id]

    # Strip nulls and empty strings, convert data:
    for f in mysql_record.keys():
        val = mysql_record[f]

        # Strip Nulls and Blanks
        if val == None or val == '':
            del mysql_record[f]
            continue

        # Do the conversion
        if f in identical_fields:
            new_user[f] = val
            continue

        if f in name_changes:
            new_f = name_changes[f]
            new_user[new_f] = val
            continue

        if f == 'birthdate':
            new_user['birthdate'] = str(val)

        if f == "address1":
            if new_address != None:
                new_address[0] = val
            else:
                new_address = [val, ""]

        if f == "address2":
            if new_address != None: 
                new_address[1] = val
            else:
                new_address = ["", val]

        if f in ["phone", "home", "cell", "work"]:
            if 'phone' in new_user:
                new_user['phone'][f] = val
            else:
                new_user['phone'] = {f: val}

        if f[:5] == 'show_':
            new_f = f[5:]
            if 'privacy' in new_user:
                new_user['privacy'][new_f] = val
            else:
                new_user['privacy'] = {new_f: val}

        if f == 'photo':
            photo_data = val
            new_user['photo'] = 'on_disk'
            del mysql_record[f]

        if f == 'height_in':
            try:
                new_user['height'] = int(val)
            except Exception:
                pass

        if f == 'weight':
            try:
                new_user['weight'] = int(val)
            except Exception:
                pass

    # Condense address
    if new_address != None:
        new_user['address'] = "\n".join(new_address).strip()

    # write to mongo users
    new_id = users_coll.save(new_user)
    print "Saved user " + str(new_id)

    # write to mongo identities if username present
    if old_identity == None and forum_username != None  :
        ident_coll.save({'type': 'phpbb', 'prv_name': 'afdc.com', 'prv_uid': forum_username, 'user_id': new_id})
        print "Saved identity: phpbb:" + forum_username 

    # write image to disk
    if photo_data != None:
        image = open(image_folder + str(new_id) + '.jpg', 'wb');
        image.write(photo_data)
        image.close()
        print "Wrote photo for " + str(new_id)

    mysql_record = old_cur.fetchone()