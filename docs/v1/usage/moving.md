# Moving a file to persistent

Moving a file has the same api as uploading

## Example
This moves the file my-history.pdf to persistent storage

- upload the file to persistent
- creates a cover
- creates variants for the cover
- deletes the local file

```php
    $localFile = 'my-history.pdf';
    $moveFileService = MoveFileService::make($localFile);
    $xid = $moveFileService->moveToPersistent('abcd');
```

Just like moving an image

- creates variants for the cover
- deletes the local file
- 
```php
    $localFile = 'awesome.png';
    $moveFileService = MoveFileService::make($localFile);
    $xid = $moveFileService->moveToPersistent('abcd');
```

