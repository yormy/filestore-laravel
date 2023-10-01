# Encrypted images

## Downloading the image
In your controller
```php
    $file = MemberFile::where('xid', $xid)->firstOrFail();
    return  FileServe::download($file->disk, $file->fullPath);
````

## Displaying image
### Retrieve the image
```php
    $file = MemberFile::where('xid', $xid)->firstOrFail();
    $imagedata = FileServe::view($file->disk, $file->fullPath, $file->mime); // ie data:image/png;base64,XXXX
```

### Return the imagedata to the view
Display with:
```html
<img src="{{imagedata}}">
```


## Display pdf
```html
  <img :src="fromEncrypted">
  <h1> igrame</h1>
  <iframe :src="fromEncrypted"/>
    <h1>Object</h1>
    <object :data="fromEncrypted"/>
<object :data="fromEncrypted" width="560" height="615"/>
```

