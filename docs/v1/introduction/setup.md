# Setup you S3 store

Create a user with the policies and add the credentials to your disk setup in config of your main app
Do not login with the root user.

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
                "s3:GetObject",
                "s3:PutObject",
                "s3:DeleteObject"
            ],
            "Resource": [
                "arn:aws:s3:::<your bucket name>/*"
            ]
        }
    ]
}
```
NOTE: ListObject is not used, so no need to enable that in the policy

### Check your setup
run:
```
php artisan filestore:check
```
This will verify that your bucket can be connected to and has no open issues.

