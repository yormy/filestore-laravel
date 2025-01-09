public function withoutVariants()
do not build thumbnails

withoutPdfPages
do not build pdf pages as images

preventPdfEmbedding
embed the actual pdf is not possible (route blocked)


withoutAccessLog


userencryption,. use key from user not from system to encrypt
        $encryptedFilename = UploadFileService::make($file)
            ->sanitize()
            ->memberId(6)
            ->userEncryption()


