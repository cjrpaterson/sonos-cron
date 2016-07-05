# sonos-cron
Service for retrieving commands from an AWS SQS queue and passing them to an instance of the Sonos HTTP API (https://github.com/jishi/node-sonos-http-api)

### Usage

1. Install dependencies with composer (`composer install`).
2. Copy `src/parameters.yml.dist` to `src/parameters.yml` and add relevant values.
3. Add a cron job to run `php src/cron.php` however frequently you would like messages to be processed.

### Handlers
Handlers are registered in order to process specific API actions, e.g. `say`, `next` and `volume`. The handler which should be called is defined by the attribute `handler` in the body of the SQS message. Currently only the `say` handler is implemented. Further handlers can be added by implementing the `HandlerInterface`, and tagging the service with `name: sonos_cron.handler`.

#### Say
The body of a message which should be handled as a `say` action is:
```
{
  "handler": "say",
  "payload": {
    "room": "Living Room",
    "message": "Hi, this will be read out by the Sonos",
    "language": "en-gb"
  }
}
```
`language` should be made up of an [ISO 639-1 language code](https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes) and [ISO 3166-1 alpha-2 country code](https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2#Officially_assigned_code_elements), concatenated with a hyphen. E.g. `en-gb`, `fr-fr`, `zh-cn`.

`room` is the name of the Sonos device you wish to target, as it is named in the Sonos controller app.
