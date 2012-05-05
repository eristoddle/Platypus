/* indices.js, note that _id is automatically indexed */
/* execute as follows: mongo Platypus < indices.js */

/* Cart Items */
db.cart_items.ensureIndex({"carts" : 1});
db.cart_items.ensureIndex({"reference_class" : 1, "reference_id" : 1});

/* Field Sites */
db.field_sites.ensureIndex({"active" : 1});

/* Games */
db.games.ensureIndex({"league_id" : 1});
db.games.ensureIndex({"teams" : 1});
db.games.ensureIndex({"game_time" : 1});

/* Identities */
db.identities.ensureIndex({"type" : 1, "prv_name" : 1, "prv_uid" : 1});
db.identities.ensureIndex({"user_id" : 1, "type" : 1, "prv_name" : 1});

/* Leagues */
db.leagues.ensureIndex({"registration_open" : 1, "registration_close" : 1});
db.leagues.ensureIndex({"start_date" : 1, "end_date" : 1});

/* Payments */
db.payments.ensureIndex({"shopping_cart_id" : 1});

/* Registrations */
db.registrations.ensureIndex({"league_id" : 1, "status" : 1, "gender" : 1});
db.registrations.ensureIndex({"league_id" : 1, "user_id" : 1});

/* Shopping Carts */
db.shopping_carts.ensureIndex({"user_id" : 1});

/* Teams */
db.teams.ensureIndex({"league_id" : 1});

/* Users Collection */
db.users.ensureIndex({"email_address" : 1}, {"sparse" : true, "unique" : true});
db.users.ensureIndex({"teams" : 1});

