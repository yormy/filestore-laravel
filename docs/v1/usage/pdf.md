# PDF 
When uploading a pdf file it automatically creates a cover png of the first page of the pdf
and creates the variants of the cover

# Options

## withPages
Convert each page to an individual image that can be served
```php
    $xid = UploadFileService::make($file)
        ->withPdfPages()
```

