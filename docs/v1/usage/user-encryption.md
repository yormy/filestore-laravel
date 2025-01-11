# User Encryption

## Goal
Use a use specific key
This will allow for greater security that if user B has access through a vulnerability (ie IDOR) somewhere to a file beloning to user A, they still cannot decrypt and read it.

## Implementation
This a 2 step approach.
First the file is encrypted with the system key
Then the file is encrypted again with the specific user key.
This will allow the admin to access the user specific key still decrypt the file for an admin.

Another user does not have access to that key, and cannot decrypt it.

In this approach the user key is stored in the database. For additional security you can even create a new userResolver 
and retrieve the encryption key from somewhere else


## Example

```
    $xid = UploadFileService::make($file)
        ->userEncryption()      // this will enable user encryption
        ->saveEncryptedToLocal('myid');
```
