const express = require('express');
const app = express();
const port = 3000;

const { v4: uuidv4 } = require('uuid');
const mysql = require('mysql2/promise');

//TODO: lookup message parameters from admin api where console logs req.params (this currently returns only an array of objects)
//TODO: implement

// fetch map details -> returns MapDetailsData
// currently, the tags and the policy type are used
app.get('/api/map', (req, res) => {
    console.log('fetchMapDetails called');
    console.log('organizationSlug: ' + req.query.organizationSlug);
    console.log('worldSlug: ' + req.query.worldSlug);
    console.log('roomSlug: ' + req.query.roomSlug);
});

// fetch member data by uuid -> returns FetchMemberDataByUuidResponse
// currently, the messages, the tags, the textures, the tags, the anonymous variable and http status codes are used
// Status codes:
// 404 -> anonymous login
// 403 -> world full
// tags: 'admin' for sending world messages, furthermore a jitsi moderator tag can be set (set in map, is a custom tag)
app.get('/api/room/access', async (req, res) => {
    console.log('fetchMemberDataByUuid called');
    console.log('uuid: ' + req.query.uuid);
    console.log('roomId: ' + req.query.roomId);

    // Get token
    var uuid = req.query.uuid;
    if (uuid === undefined) {
        uuid = uuidv4();
    }

    // create account on first connection
    var userExistsValue = await userExists(uuid);
    if (!userExistsValue) {
        console.log('User with uuid ' + uuid + ' does not exist. Creating account.');
        writeUuidToDatabase(uuid);
    }

    // Get tags
    let tags = [];
    var isAdminValue = await isAdmin(uuid);
    if (isAdminValue) {
        console.log('User with uuid ' + uuid + ' is an admin. Pushing admin tag.');
        tags.push('admin');
    } else {
        console.log('User with uuid ' + uuid + ' is not an admin.');
    }

    const result = {
        uuid: uuid,
        tags: tags, //User tags. None -> normal user. 'admin' -> admin user. I don't know whether there are more options
        textures: [{//something with textures, maybe some filter to restrict textures
            id: 1,
            level: 1,
            url: "resources/characters/pipoya/Male 01-1.png",
            rights: "",
        }],
        messages: [],// send messages to the client
        anonymous: false,
    };

    res.json(result);
});

// fetch member data by token -> returns AdminApiData
// maybe useful for generating the admin join links
// currently, the uuid, the organization slug, the world slug, the room slug,
// the mapUrlStart and the textures are being considered
app.get('/api/login-url/:token', (req, res) => {
    console.log('fetchMemberDataByToken called');
    console.log('token: ' + req.params.token);
});

// fetch check user by token -> returns AdminApiData
// as of now, only the uuid and the http status code of the result are being evaluated
// if this API method is being called
app.get('/api/check-user/:token', async (req, res) => {
    console.log('fetchCheckUserByToken called');
    console.log('token: ' + req.params.token);

    // Get uuid ( = token)
    var uuid = req.params.token;
    if (uuid == undefined) {
        uuid = uuidv4();
    }

    // create account on first connection
    var userExistsValue = await userExists(uuid);
    if (!userExistsValue) {
        console.log('User with uuid ' + uuid + ' does not exist. Creating account.');
        writeUuidToDatabase(uuid);
    }

    // Get tags
    let tags = [];
    var isAdminValue = await isAdmin(uuid);
    if (isAdminValue) {
        console.log('User with uuid ' + uuid + ' is an admin. Pushing admin tag.');
        tags.push('admin');
    } else {
        console.log('User with uuid ' + uuid + ' is not an admin.');
    }

    const result = {
        organizationSlug: "HSM", //?
        roomSlug: "IoT-Lab", //?
        mapUrlStart: "maps/gaming/map.json",// probably the URL to the start map
        tags: tags, //User tags. None -> normal user. 'admin' -> admin user. I don't know whether there are more options
        policy_type: 1,//?
        userUuid: uuid,// UserId? -> This must be unique.
        messages: [],//??
        textures: [{//something with textures, maybe some filter to restrict textures
            id: 1,
            level: 1,
            url: "resources/characters/pipoya/Male 01-1.png",
            rights: "",
        }],
    };
    res.json(result);
});

// report player -> returns ?
// no result object -> not used by the client
// does not seem to work properly as of now
app.post('/api/report', (req, res) => {
    console.log('reportPlayer called');
    console.log('reportedUserUuid: ' + req.query.reportedUserUuid);
    console.log('reportedUserComment: ' + req.query.reportedUserComment);
    console.log('reporterUserUuid: ' + req.query.reporterUserUuid);
    console.log('reportWorldSlug: ' + req.query.reportWorldSlug);
});

// verify ban -> returns AdminBannedData
// only works on private maps -> '@' url part
// is_banned is used, message not but most likely equal to ban message
app.get('/api/check-moderate-user/:organization/:world', (req, res) => {
    console.log('verifyBanUser called');
    console.log('organization: ' + req.params.organization);
    console.log('world: ' + req.params.world);
    console.log('ipAddress: ' + req.query.ipAddress);
    console.log('token: ' + req.query.token);
});

app.listen(port, () => {
    console.log(`Admin API listening on port ${port}`);
});

async function createConnection() {
    return connection = await mysql.createConnection({
        host     : 'admin-db',
        user     : process.env.DB_MYSQL_USER,
        password : process.env.DB_MYSQL_PASSWORD,
        database : process.env.DB_MYSQL_DATABASE
    });
}

async function userExists(uuid) {
    console.log('userExists called');

    result = false;
    connection = await createConnection();
    await connection.connect();

    const[rows, files] = await connection.execute('SELECT * FROM `USERS` WHERE `uuid` = ?', [uuid]);
    if (rows.length > 0) {
        console.log('Found uuid ' + rows[0].uuid);
        result = true;
    } else {
        console.log('Did not find uuid ' + uuid);
        result = false;
    }

    await connection.end();

    return result;
}

async function writeUuidToDatabase(uuid) {
    console.log('writeUuidToDatabase called');

    connection = await createConnection();
    await connection.connect();

    await connection.execute('INSERT INTO `USERS` (`uuid`, `isadmin`) VALUES (?, ?)', [uuid, false]);
    await connection.end();
}

async function isAdmin(uuid) {
    console.log('isAdmin called');

    result = false;
    connection = await createConnection();
    await connection.connect();

    const[rows, files] = await connection.execute('SELECT * FROM `USERS` WHERE `uuid` = ?', [uuid]);
    if (rows.length > 0)  {
        if (rows[0].uuid == uuid) {
            if (rows[0].isadmin == true) {
                result = true;
            }
        }
    }

    await connection.end();
    return result;
}
