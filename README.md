# GmailBundle

[![StyleCI](https://styleci.io/repos/70251410/shield?branch=master)](https://styleci.io/repos/70251410)

GmailBundle allows you to manage a Google Apps domain's inboxes (you can pick which). In order to do this, you must authorize 
your Symfony application, with a Google Apps admin account. Domain-wide delegation must be authorized, [What is domain-wide delegation?](https://developers.google.com/+/domains/authentication/delegation)

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
    redirect_route_after_save_authorisation: some_route_name__not_a_url
    gmail_message_class: \AppBundle\Entity\GmailMessage
    gmail_label_class: \AppBundle\Entity\GmailLabel
    gmail_history_class: \AppBundle\Entity\GmailHistory
    gmail_ids_class: TriprHqBundle\Entity\GmailIds
    credentials_storage_service: fl_gmail_doctrine.credentials_storage
    
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

#### Authorizing
- This bundle comes with two controller services, which you can find a routing file for at `Resources/config/routing.yml`
- `FL\GmailBundle\Action\AuthoriseGoogleAction` redirects the end-user (Google Apps admin) to a Google page where they authorize the Symfony application.
- `FL\GmailBundle\Action\SaveAuthorisationAction` saves the authorization, once the user is redirected from the aforementioned Google page.
- It is your responsibility, to provide a service that saves this authorization. See `credentials_storage_service` key in configuration.
- This service, must implement `FL\GmailBundle\Storage\CredentialsStorageInterface`.

#### Syncing Gmail Ids (i.e. Which emails need to be synced?)
- `FL\GmailBundle\Services\SyncGmailIds`
    - Gets a list of all the Gmail Ids, or the subset of Gmail Ids according to a history Id.  [What is a history Id?](https://developers.google.com/gmail/api/guides/sync)
    - Dispatches `FL\GmailBundle\Event\GmailSyncIdsEvent` with a list of all the new / updated ids. (Updated Gmail Ids = change of label)
    - It is your responsibility to save the Gmail Ids coming from this event.
    - Dispatches `FL\GmailBundle\Event\GmailSyncHistoryEvent`, such that next time, you can perform a Partial Sync. [What is a Partial Sync?](https://developers.google.com/gmail/api/guides/sync)
    - It is your responsibility to save the History Id coming from this event.

#### Syncing Messages using Gmail Ids (i.e. I know which emails need to be synced, let's fetch them.)
- `FL\GmailBundle\Services\SyncMessages`
    - This service, takes a list of gmail ids and resolves all the new/updated messages for you. 
    - I.e. use the ids you are fetching from `FL\GmailBundle\Services\SyncGmailIds`
    - Dispatches `FL\GmailBundle\Event\GmailSyncMessagesEvent`.
    - It is your responsibility to save the Gmail Messages coming from this event.
    - It is your responsibility to remove the newly synced Gmail Ids, you had previously saved at `FL\GmailBundle\Services\SyncGmailIds`.

#### All this responsibility? :cry: :sob:

Why are there so many `It is your responsibility` statements? Because this bundle is storage agnostic. But don't fret! There 
is a [GmailDoctrineBundle](https://github.com/fourlabsldn/GmailDoctrineBundle) that implements all of this in Doctrine for you. 

## License

GmailBundle is licensed under the MIT license.

