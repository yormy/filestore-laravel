# For User : todo
This assigns a user to the file record.
This is for future use cases to limit the scope of certain files to certain users.
Maybe rename this to setOwner to make it clear what the data does.
```php
->forUser ($user)
```

Related, but not the same userEncryption($user)
This uses $user to get the encryption key that is needed to decrypt the first step
