import pymongo

def getMongoId(collection, mysql_id, cache = {}, mysql_field_name = 'mysql_id'):
    if mysql_id in cache:
        return cache[mysql_id]

    found_doc = collection.find_one({mysql_field_name : mysql_id})
    if found_doc != None:
        found_doc_id = found_doc['_id']
        cache[mysql_id] = found_doc_id
        return found_doc_id

    return None    

