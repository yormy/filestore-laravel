# Basic setup


ListObject is not used, so no need to enable that in the policy

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
