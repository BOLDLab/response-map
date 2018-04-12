# Response Map

The response map is an LTI tool that allows students to respond to a question or give feedback and have the responses show up on a world map based on the location that they enter in. All the response are also processed and turned into a word cloud at the bottom of the map. Students can also upload an image along with their response.

This code base is adapted from previous versions to allow easy deployment to Heroku, using an AWS S3 bucket for image storage and a JawsDB hosted mySQL database.

## Requirements
* Get a Heroku account at [heroku.com](https://heroku.com)
* [Create a Dyno](https://www.heroku.com/pricing) that fits your requirements.
* Create a new PHP app in Heroku
* Add the JawsDB MySQL resource
* Create an Amazon S3 bucket with appropriate permissions

Then login to the heroku cli
```bash
heroku login
Enter your Heroku credentials:
Email: [your email here]
Password: [your password here]
Two-factor code: [if required]
Logged in as you@your email address
```
## Installation

Clone this repo:

```bash
git clone git@github.com:BOLDLab/response-map.git
cd response-map
```

##### Connection String
First, you must create a shell script containing the environment variables for the application.

```bash
#!/bin/bash
heroku config:set LTI_KEY=lti_key LTI_SECRET=lti_secret GOOGLE_API_KEY=YoufadfdajklAPIKEyejkldsafjljoi MYSQL_DB_HOST=yourMysqlhost.somewhere.rds.amazonaws.com MYSQL_DB_NAME=dbname MYSQL_DB_USER=dbuser MYSQL_DB_PASSWORD=dbpassword DB_ADMIN_PASSWORD=dbadminpass AWS_ACCESS_KEY=awsaccesskey AWS_SECRET_KEY=awssecretkey AWS_REGION=us-east-1 S3_BUCKET=yourS3bucket
```

Get the host and database name from the connection string (former is host latter is DB name):

<span style='font-family: courier, monospace'> mysql://jdkfjlsa-039:8hloejnvbbjbe@**0kpwnvhuh.hehuvuh.eu-west-1.rds.amazonaws.com**:3306/**nnjdn8yh83hhb**</span>

Set the `$allowed_origins` array to contain the allowed origins for your app.

Then, you must specify and LTI key and secret in lti.php.

### Deploy to Heroku
```bash
#!/bin/bash
rm -rf deploy
mkdir deploy

gfind . -path ./deploy -prune -o -type f -exec gcp --parents -t deploy {} +

cd deploy

git pull heroku master
git commit -m 'heroku deploy'
git push heroku master

bash ./heroku-config-set.sh #use the shell script you created earlier
```

Within your course in edX Studio, the LTI module must be enabled in order to create LTI components. This can be done by going to Settings > Advanced Settings and adding ```"lti"``` to the array.

Also under Advanced Settings, the LTI Passports array must contain the LTI key and secret pair that is used by the tool. You must add it to the array in the following format: ```"passport_id:key:secret"```. The id is later used when configuring the LTI component to obtain the key and secret.

Next, create the LTI component within a course unit (under Add New Component > Advanced > LTI) and click on "Edit". Make sure to enter in the the LTI ID that you have previously set in LTI Passport. Specify the url to the tool (make sure you have a closing slash) and turn off opening in a new page for a seamless look. If you would like to give a student a partipation mark for responding to the response-map, then set the "Scored" attribute to true.

## Workflow
<img src="https://github.com/UQ-UQx/response-map/blob/master/README_WORKFLOW_IMAGE.png?raw=true">

##License
This project is licensed under the terms of the MIT license.

##Contact
The best contact point apart from opening github issues or comments is to email <a href='mailto:bold@newcastle.edu.au'>bold@newcastle.edu.au</a>
