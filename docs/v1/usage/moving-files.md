# Moving files around
Sometimes you want to move files to and from persistent storage

## Move to persistent
Basically this works the same as uploading
This is how you move $localfile to persistent with userencryption
```
        $moveFileService = MoveFileService::make($localFile);
        $moveFileService->encrypted(true);
        $moveFileService->userEncryption($user);
        $xid = $moveFileService->moveToPersistent('abcd');
```

# Move from persistent to local
When moving and decrypting a file you need to pass in the user that encrypted the file, this is to allow for user encryption fields.
```
$localFilename = FileGet::getFile(xid: $xid, user: $user); // download file to local
```
