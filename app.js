const express = require('express');
const app = express();
const port = 3000;

//TODO: lookup message parameters from admin api where console logs req.params (this currently returns only an array of objects)
//TODO: implement

//WARNING: this is a test implemantion. With this, everyone joins as admin.
// fetch map details
app.get('/api/map', (req, res) => {
    console.log('fetchMapDetails called');
    console.log('params: ' + req.params);
});

// fetch member data by uuid
app.get('/api/room/access', (req, res) => {
    console.log('fetchMemberDataByUuid called');
    //both parameters can be undefined
    console.log('uuid: ' + req.params.uuid);
    console.log('roomId: ' + req.params.roomId);
    
    const result = {
        uuid: req.params.uuid,// UserId? -> This must be unique.
        tags: ['admin'], //User tags. None -> normal user. 'admin' -> admin user. I don't know whether there are more options
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

// fetch member data by token
app.get('/api/login-url/:token', (req, res) => {
    console.log('fetchMemberDataByToken called');
    console.log('params: ' + req.params);
});

// fetch check user by token
app.get('/api/check-user/:token', (req, res) => {
    console.log('fetchCheckUserByToken called');
    console.log('token: ' + req.params.token);

    const result = {
        organizationSlug: "HSM", //?
        roomSlug: "IoT-Lab", //?
        mapUrlStart: "maps/work/map.json",// probably the URL to the start map
        tags: ['admin'], //User tags. None -> normal user. 'admin' -> admin user. I don't know whether there are more options
        policy_type: 1,//?
        userUuid: "uuid",// UserId? -> This must be unique.
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

// report player
app.get('/api/report', (req, res) => {
    console.log('reportPlayer called');
    console.log('params: ' + req.params);
});

// verify ban
app.get('/api/check-moderate-user/:organization/:world', (req, res) => {
    console.log('verifyBanUser called');
    console.log('params: ' + req.params);
    console.log('ipAddress: ' + req.query.ipAddress);
    console.log('token: ' + req.query.token);
});

app.listen(port, () => console.log(`Admin API stub listening on port ${port}`));
