# GmailBundle

[![StyleCI](https://styleci.io/repos/70251410/shield?branch=master)](https://styleci.io/repos/70251410)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/2113fe3d-8256-4009-8d0c-8a84f21a7b59/mini.png)](https://insight.sensiolabs.com/projects/2113fe3d-8256-4009-8d0c-8a84f21a7b59)

GmailBundle allows you to manage a Google Apps domain's inboxes (you can pick which). In order to do this, 
you must authorize a [service account with domain wide delegation](https://console.developers.google.com/iam-admin/serviceaccounts/serviceaccounts-zero)

## Installation

```bash
    $ composer require fourlabs/gmail-bundle
```

## Configuration

```yaml
// app/config/config.yml
fl_gmail:
    admin_user_email: tech@slv.global
    json_key_location: /var/www/symfony/app/config/service_account_private_key.json
    gmail_message_class: \AppBundle\Entity\GmailMessage
    gmail_label_class: \AppBundle\Entity\GmailLabel
    gmail_history_class: \AppBundle\Entity\GmailHistory
    gmail_ids_class: \AppBundle\Entity\GmailIds
    
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

#### Syncing Gmail Ids (i.e. Which emails need to be synced?)
`FL\GmailBundle\Services\SyncGmailIds`
- Takes a `$userId` parameter. 
- Gets a list of all the Gmail Ids, or the subset of Gmail Ids according to a history Id.  [What is a history Id?](https://developers.google.com/gmail/api/guides/sync)
- Dispatches `FL\GmailBundle\Event\GmailSyncIdsEvent` with a list of all the new / updated ids. (Updated Gmail Ids = change of label)
- It is your responsibility to save the Gmail Ids coming from this event.
- Dispatches `FL\GmailBundle\Event\GmailSyncHistoryEvent`, such that next time, you can perform a Partial Sync. [What is a Partial Sync?](https://developers.google.com/gmail/api/guides/sync)
- It is your responsibility to save the History Id coming from this event.

#### Syncing Messages using Gmail Ids (i.e. I know which emails need to be synced, let's fetch them.)
`FL\GmailBundle\Services\SyncMessages`
- This service, takes a list of gmail ids and resolves all the new/updated messages for you. 
- I.e. use the ids you are fetching from `FL\GmailBundle\Services\SyncGmailIds`
- Dispatches `FL\GmailBundle\Event\GmailSyncMessagesEvent`.
- It is your responsibility to save the Gmail Messages coming from this event.
- It is your responsibility to remove the newly synced Gmail Ids, you had previously saved with `FL\GmailBundle\Services\SyncGmailIds`.

#### All this responsibility? :cry: :sob:

Why are there so many `It is your responsibility` statements? Because this bundle is storage agnostic. But don't fret! There 
is a [GmailDoctrineBundle](https://github.com/fourlabsldn/GmailDoctrineBundle) that implements all of this in Doctrine for you. 

### How do I dive into this bundle?

- Start by looking into the `Model` classes.
- To understand the services, have a look at `Resources/config/services.yml`.

### What else is going on?

- You can send swiftmailer emails through `FL\GmailBundle\Swift\GmailApiTransport`. Simply make sure the from is in your domain.
- `FL\GmailBundle\Form\Type\InboxType` contains a choice type, with all the inboxes in the authenticated domain.

## License

GmailBundle is licensed under the MIT license.

