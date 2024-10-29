const { google } = require('googleapis');
const fs = require('fs');
const express = require('express');
const bodyParser = require('body-parser');
const path = require('path');
require('dotenv').config();

const app = express();
app.use(bodyParser.urlencoded({ extended: true }));

const SCOPES = ['https://www.googleapis.com/auth/gmail.send'];
const TOKEN_PATH = path.join(__dirname, '../token.json'); // Adjust the path to your token.json

const auth = new google.auth.OAuth2({
    clientId: process.env.CLIENT_ID,
    clientSecret: process.env.CLIENT_SECRET,
    redirectUri: "http://localhost:8080/oauth2callback"
});

fs.readFile(path.join(__dirname, '../credentials.json'), (err, content) => {
    if (err) return console.error('Error loading client secret file:', err);
    authorize(JSON.parse(content), startServer);
});

function authorize(credentials, callback) {
    const { client_secret, client_id, redirect_uris } = credentials.web; 
    const oAuth2Client = new google.auth.OAuth2(client_id, client_secret, redirect_uris[0]);

    fs.readFile(TOKEN_PATH, (err, token) => {
        if (err) {
            return getAccessToken(oAuth2Client, callback);
        }
        oAuth2Client.setCredentials(JSON.parse(token));
        callback(oAuth2Client);
    });
}

function getAccessToken(oAuth2Client, callback) {
    const authUrl = oAuth2Client.generateAuthUrl({
        access_type: 'offline',
        scope: SCOPES,
    });
    console.log('Authorize this app by visiting this url:', authUrl);
}

app.get('/oauth2callback', (req, res) => {
    const code = req.query.code;
    oAuth2Client.getToken(code, (err, token) => {
        if (err) return console.error('Error retrieving access token', err);
        oAuth2Client.setCredentials(token);
        fs.writeFileSync(TOKEN_PATH, JSON.stringify(token));
        res.send('Authentication successful! You can close this tab.');
    });
});

function startServer(auth) {
    const gmail = google.gmail({ version: 'v1', auth });

    app.post('/send-email', (req, res) => {
        const to = 'nap.cbaylosis@gmail.com';
        const name = req.body.name || 'No Name';
        const userEmail = req.body.email || 'no-reply@example.com';
        const subject = req.body.subject || 'No Subject';
        const messageText = req.body.message || 'No Message';

        const email = [
            `From: ${userEmail}`,
            `To: ${to}`,
            `Subject: ${subject}`,
            `Reply-To: ${userEmail}`,
            '',
            `Message from ${name}:\n\n${messageText}`
        ].join('\r\n');
        
        const rawMessage = Buffer.from(email).toString('base64').replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');

        const message = {
            userId: 'me',
            resource: {
                raw: rawMessage,
            },
        };

        gmail.users.messages.send(message, (err, response) => {
            if (err) {
                console.error('An error occurred while sending the email:', err);
                return res.status(500).send('Error sending email: ' + err.message);
            }
            res.send('Email sent successfully!');
        });
    });

    app.listen(8080, () => {
        console.log('Server running on http://localhost:8080');
    });
}