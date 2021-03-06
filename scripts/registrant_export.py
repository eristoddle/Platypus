import pymongo
from bson.objectid import ObjectId
import pprint, sys, json, os, codecs

if (len(sys.argv) > 1):
    try:
        league_id = ObjectId(sys.argv[1])
    except:
        print "Invalid league ID."
        sys.exit()
else:
    print "Please enter a league id to do the export."
    sys.exit()

if (len(sys.argv) > 2):
    f  = codecs.open(sys.argv[2], 'w', 'UTF-8')
else:
    f = None

config_json = open('../config/config.json')
config      = json.load(config_json)
config_json.close()

mysql_config = config['mysql']
mongo_config = config['mongo']

new_con       = pymongo.Connection()
platypus_db   = new_con[mongo_config['database']]
leagues       = platypus_db['leagues']
registrations = platypus_db['registrations'] 
users         = platypus_db['users']

league = leagues.find_one({"_id" : league_id})

if league == None:
    print "League not found."
    sys.exit()

header_row = "user_id,draft_id,team_id,gRank,self-rank,first name,last name,email,gender,availability,pair,notes,birthdate,height,MST,EOST,wants competitive, wants social, wants family, gRank exp, gRank ath, gRank lvl, gRank skill"

if f == None:
    print header_row
else:
    f.write(header_row + "\n")

for reg in registrations.find({"league_id" : league_id, "status" : "active"}):
    user = users.find_one({"_id" : reg["user_id"]});

    if "height" not in user:
        user['height'] = ""

    if "birthdate" not in user:
        user['birthdate'] = ""

    if "email_address" not in user:
        user['email_address'] = ""

    fields = [
        str(reg['user_id']),
        "",
        "",
        str(reg['secondary_rank_data']['grank']),
        str(reg['secondary_rank_data']['self_rank']),
        user['firstname'],
        user['lastname'],
        user['email_address'],
        reg['gender'],
        reg['availability']['general'],
        '"' + reg['pair']['text'] + '"',
        '"' + reg['notes'] + '"',
        user['birthdate'],
        str(user['height']),
        str(reg['availability']['attend_tourney_mst']),
        str(reg['availability']['attend_tourney_eos']),
        str(reg['team_style_pref']['competitive']),
        str(reg['team_style_pref']['social']),
        str(reg['team_style_pref']['family']),
        str(reg['gRank']['answers']['experience']),
        str(reg['gRank']['answers']['athleticism']),
        str(reg['gRank']['answers']['level_of_play']),
        str(reg['gRank']['answers']['ultimate_skills']),
    ]
    line = ",".join(fields)
    if f == None:
        print line
    else:
        f.write(line + "\n")

if f != None:
    f.close()