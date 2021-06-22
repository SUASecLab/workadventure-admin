const express = require('express');
const app = express();
const port = 3000;

const { v4: uuidv4 } = require('uuid');
const mysql = require('mysql2/promise');

//TODO: lookup message parameters from admin api where console logs req.params (this currently returns only an array of objects)
//TODO: implement

//WARNING: this is a test implemantion. With this, everyone joins as admin.
// fetch map details -> returns MapDetailsData
app.get('/api/map', (req, res) => {
    console.log('fetchMapDetails called');
    console.log('organizationSlug: ' + req.query.organizationSlug);
    console.log('worldSlug: ' + req.query.worldSlug);
    console.log('roomSlug: ' + req.query.roomSlug);
});

// fetch member data by uuid -> returns FetchMemberDataByUuidResponse
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
    
   // return res.status(404).json({ error: 'test' });
    
    /*
     * return 404 to force an anonymous login
     * return 403 if a yet to be specified number of maximal users has been reached
     */
    res.json(result);
});

// fetch member data by token -> returns AdminApiData
app.get('/api/login-url/:token', (req, res) => {
    console.log('fetchMemberDataByToken called');
    console.log('token: ' + req.params.token);
});

// fetch check user by token -> returns AdminApiData
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
        mapUrlStart: "maps/work/map.json",// probably the URL to the start map
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
app.post('/api/report', (req, res) => {
    console.log('reportPlayer called');
    console.log('reportedUserUuid: ' + req.query.reportedUserUuid);
    console.log('reportedUserComment: ' + req.query.reportedUserComment);
    console.log('reporterUserUuid: ' + req.query.reporterUserUuid);
    console.log('reportWorldSlug: ' + req.query.reportWorldSlug);
});

// verify ban -> returns AdminBannedData
app.get('/api/check-moderate-user/:organization/:world', (req, res) => {
    console.log('verifyBanUser called');
    console.log('organization: ' + req.params.organization);
    console.log('world: ' + req.params.world);
    console.log('ipAddress: ' + req.query.ipAddress);
    console.log('token: ' + req.query.token);
});

app.listen(port, () => {
    console.log(`Admin API stub listening on port ${port}`);
    console.log('ADMIN_DB_MYSQL_ROOT_PASSWORD: ' + process.env.DB_MYSQL_ROOT_PASSWORD);
    console.log('ADMIN_MYSQL_DATABASE: ' + process.env.DB_MYSQL_DATABASE);
    console.log('ADMIN_MYSQL_USER: ' + process.env.DB_MYSQL_USER);
    console.log('ADMIN_MYSQL_PASSWORD: ' + process.env.DB_MYSQL_PASSWORD);
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
