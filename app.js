const express = require('express');
const app = express();
const port = 3000;

const { v4: uuidv4 } = require('uuid');

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
app.get('/api/room/access', (req, res) => {
    console.log('fetchMemberDataByUuid called');
    console.log('uuid: ' + req.query.uuid);
    console.log('roomId: ' + req.query.roomId);

    // Get token
    var uuid = req.query.uuid;
    if (uuid === undefined) {
        uuid = uuidv4();
    }

    // Get tags
    let tags = []
    if (isAdmin(uuid)) {
        tags.push('admin');
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
        messages: [],//??
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
app.get('/api/check-user/:token', (req, res) => {
    console.log('fetchCheckUserByToken called');
    console.log('token: ' + req.params.token);

    // Get token
    var token = req.params.token;
    if (token == undefined) {
        token = uuidv4();
    }

    // Get tags
    let tags = [];
    if (isAdmin(token)) {
        tags.push('admin');
    }

    const result = {
        organizationSlug: "HSM", //?
        roomSlug: "IoT-Lab", //?
        mapUrlStart: "maps/work/map.json",// probably the URL to the start map
        tags: tags, //User tags. None -> normal user. 'admin' -> admin user. I don't know whether there are more options
        policy_type: 1,//?
        userUuid: token,// UserId? -> This must be unique.
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

app.listen(port, () => console.log(`Admin API stub listening on port ${port}`));


function isAdmin(uuid) {
    return true;
}
