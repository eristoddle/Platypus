#
# Source:      field        MySQL
# Destination: fieldsites   Mongo
#

import pymongo
from bson.objectid import ObjectId
import pprint, sys, json, os

config_json = open('../../config/config.json')
config      = json.load(config_json)
config_json.close()

mongo_config = config['mongo']

new_con        = pymongo.Connection()
platypus_db   = new_con[mongo_config['database']]
registrations = platypus_db['registrations']
cart_items    = platypus_db['cart_items']
payments      = platypus_db['payments']

reg_list = registrations.find({"league_id" : ObjectId("4f9ffe2b406eb74b4100001d")})

for reg in reg_list:
    payment_list = []
    payment_timestamps = {}

    ci  = cart_items.find_one({"reference_id" : reg["_id"]})
    if ci:
        cart_payments = payments.find({"shopping_cart_id" : {"$in" : ci["carts"]}})

        for pmnt in cart_payments:
            payment_list.append(pmnt["_id"])
            payment_timestamps[pmnt["payment_status"].lower()] = pmnt["payment_date"]

        reg["payments"] = payment_list
        reg["payment_timestamps"] = payment_timestamps

    registrations.save(reg)
