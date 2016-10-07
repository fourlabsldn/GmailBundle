# GmailBundle

## Installation

```bash
    $ composer require fourlabs/gmail-bundle
```

## Configuration

```yaml
// app/config/config.yml
fl_gmail:
    application_name: YourGoogleApplicationName
    client_id: numbers-and-letters-that-make-up-your-client-id.apps.googleusercontent.com
    client_secret: clientSecret
    redirect_uri: http://example.com/save-access-token # must be the same as route fl_gmail.save_access_token:
    gmail_message_class: \AppBundle\Entity\GmailMessage
    gmail_label_class: \AppBundle\Entity\GmailLabel
    gmail_history_class: \AppBundle\Entity\GmailHistory
    
swiftmailer:
    default_mailer: general_mailer
    mailers:
        general_mailer:
            transport: "%mailer_transport%"
            host:      "%mailer_host%"
            username:  "%mailer_user%"
            password:  "%mailer_password%"
            spool:     { type: memory }
        fl_gmail_api_mailer:
            transport: fl_gmail.swift_transport
```

```yaml
// app/config/routing.yml
fl_gmail:
    resource: "@FLGmailBundle/Resources/config/routing.yml"
    prefix:   /your-prefix
```

## License

GmailBundle is licensed under the MIT license.

