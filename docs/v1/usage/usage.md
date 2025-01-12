
# Basic usage
## Local
upload file
download file
view file
delete file

# move local file
to , persistet/ encrypted

# Handling image files
Image variants
# Handling pdf files

# stream view
# display file
# blade + vue + html

## What are variants
Upload, create variants
download/ view

## Persistent

# Large Files
Uploading , encryption, downloading

# Advanced
- encryption settings

# Security
## - allowed files
config + per upload
how to set restrictions
Upload is a subset restriction of config allowed types + size

## sanitization
Describe how this works

# up/Download indicators


- copy code pieces in this doc
- 

# Reencrypt
# Encrypt files that are not yet encrypted (reencrypt while local key is null)



# Definitions
Encryption key variations

## System key
There is a FILESTORE_KEY (or APP_KEY if not defined) which is used for all encryptions. 
The extension will be ```.xfile```

**PRO** 
- easy

**CON**
- 1 key for all users and all encryptions

## User key
The encryption key is retrieved from the database (or create your own store somewhere). This key is used for encryption.
The extension will be ```.xufile```

**PRO**
- Each user has their own key
- User A cannot decrypt a file belonging to User B

**CON**
- The key might be stored in an insecure place

## Double Encryption (System + User)
This layers the first 2 approaches. First it encrypts with the system key, then it encrypts also with user key.
The extension will be ```.x2file```

**PRO**
- Each user has their own key
- User A cannot decrypt a file belonging to User B
- Admin can still decrypt (as the user key is retreived from the database)

**CON**
- Complexer
- Encryption takes twice as long

## User supplied key
The user supplies a key that is used. This is supplied through an input field or api call and is not stored anywhere
The extension will be ```.xsfile```

**PRO**
- Each user has their own key

**CON**
- Admin cannot decrypt
- If they loose the key, they loose the file


# PGP
- TODO
- The extension will be ```.xgfile```

# Passphrase
A user creates a passphase that will be used as a keyset
The extension will be ```.xpfile```
Basically this is the same as a user supplied key, but a passphrase can be remembered, a key cannot.


| ext     | type of encryption                |
|---------|-----------------------------------|
| .xfile  | basic encryption, system key      |
| .x2file | Double encryption, system + user |
| .xsfile | User supplied key                 |
| .xpfile | Passphrase supplied by user      |
| .xufile | Userkey from database             |
| .xgfile | Pgp                               |


# ------------------------------------------------------
# User Key Encryption
You can specify to use UserKey encryption for the encryption to use the non system key but a key per user.
They key is retrieved through the UserKeyResolver which you can customize as you wish and set the config value to use your version.
Otherwise it is store in the database.
So you can even retrieve the UserKey from a remote source.


# Access Logs
Whenever a file is accessed for download or viewing an access record is created.
You can disable the access log for individual uploaded files

```php
    $xid = UploadFileService::make($file)
        ->withoutAccessLog()
```


# Deletion
When you delete the record, all associated files will also be deleted
```php
     (new FilestoreFileRepository)->destroy($xid);
```
