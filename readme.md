# Event Sourcing

_Embrace change. Code with the flow. A field guide to creating an event store in [Laravel PHP](http://laravel.com)._

![Event Sourcing](https://pbs.twimg.com/tweet_video_thumb/DcIxd9TV4AAqV9v.jpg)

> **[Read the Blog Post](http://artisanscollaborative.test/blog/2018/05/08/event-sourcing-dallas-php):** Slides, photos, and video of the meetup are available on [Artisans Collaborative](http://artisanscollaborative.test/blog/2018/05/08/event-sourcing-dallas-php).

Does this sound familiar? Your boss keeps asking for more business intelligence from your consumer's information but at every turn you're unable to fill in the data gaps. Perhaps you've heard of DDD, CQRS, and event sourcing as possible solutions. Your interest is peaked but it seems like your favorite framework is just not setup to give you the needed tooling. Furthermore the new names and concepts are confusing and you're having a hard time keeping it all straight. It's like you're coding against the flow.

Don't worry, you're in good company. This repo is the companion codebase to a talk given at the Dallas PHP meetup where, we went step-by-step building up an event store. We wrote the code needed to get started with event sourcing data models using automobiles as a non-trivial example case for our exploration of aggregates. We manufactured ourselves a truck and registered it with the DMV, storing all the changes as events along the way. Then we read back the stream of events to recreate a moment-in-time representation of our truck. We then rolled back to before it got into an accident and streamed forward to after it was re-painted - like it never happened.

If you follow along, you too will learn how to:

- Work with Aggregates, Events, Streams, Snapshots, and Projections
- Create an event store table
- Project an event stream for a read model
- Optimize an event stream with snapshots
- Apply CQRS by using Commands and Queries
- Build a JSON endpoint to expose our projections
- Get all the source code to start event sourcing immediately

Let's create an event store and start coding _with_ the flow.

# Getting Started

- [Installation](#installation)
- [Events Table (Store)](#events-table-store)
- [Snapshots Table (Snapshot)](#snapshots-table-snapshot)
- [Trucks Table (Projection)](#trucks-table-projection)
- [Carriers Table (Projection)](#carriers-table-projection)
- [Fleets Table (Projection)](#fleets-table-projection)
- [License](#license)

> If you need more help you can ask [@dalabarge](http://twitter.com/dalabarge).

## Installation

You can clone down this repository and run:

```
cp .env.example .env
composer install
php artisan key:generate
npm install
npm run dev
```

Then edit your `.env` to meet your preferences and environment setup. The relevant code is most in the `/app` directory under the `App\Store\Contracts`, `App\Carrier`, and `App\Truck` namespaces. When you are ready to setup the database just run `php artisan migrate` to create the tables.

## Events Table (Store)

This is the global table where all events are stored.

| id | aggregate | uuid | type | payload | created_at |
| --- | --- | --- | --- | --- | --- |
| 1 | App\Truck\Aggregates\Truck | 460b0c4e-b5a1-4517-8514-cc514038d5f1 | App\Truck\Events\Created | {'uuid':'460b0c4e-b5a1-4517-8514-cc514038d5f1'} | 2018-03-31 09:00:00 |
| 2 | App\Truck\Aggregates\Truck | 460b0c4e-b5a1-4517-8514-cc514038d5f1 | App\Truck\Events\VINAssigned | {'vin':'12345678901234567'} | 2018-03-31 09:00:00 |
| 3 | App\Truck\Aggregates\Truck | 460b0c4e-b5a1-4517-8514-cc514038d5f1 | App\Truck\Events\UnitUpdated | {'unit':1} | 2018-03-31 09:00:00 |
| 4 | App\Truck\Aggregates\Truck | 460b0c4e-b5a1-4517-8514-cc514038d5f1 | App\Truck\Events\ColorChanged | {'color':'blue'} | 2018-03-31 09:00:00 |
| 5 | App\Truck\Aggregates\Truck | 460b0c4e-b5a1-4517-8514-cc514038d5f1 | App\Truck\Events\TagRegistered | {'tag':{'number':'XYZ 123','expires':'2019-03-31','region':'US-TX'}} | 2018-03-31 09:00:00 |
|  |  |  |  |  |  |
| 6 | App\Truck\Aggregates\Truck | 460b0c4e-b5a1-4517-8514-cc514038d5f1 | App\Truck\Events\Repainted | {'color':'red'} | 2018-04-15 14:23:00 |
|  |  |  |  |  |  |
| 7 | App\Truck\Aggregates\Truck | 460b0c4e-b5a1-4517-8514-cc514038d5f1 | App\Truck\Events\UnitUpdated | {'unit':2} | 2018-05-10 18:00:00 |
|  |  |  |  |  |  |
| 8 | App\Carrier\Aggregates\Carrier | b61cda06-90b1-4c0c-8670-8047fd83d1d8 | App\Carrier\Events\Created | {'uuid':'b61cda06-90b1-4c0c-8670-8047fd83d1d8','usdot':1234567} | 2018-05-10 19:00:00 |
|  |  |  |  |  |  |
| 9 | App\Truck\Aggregates\Truck | 460b0c4e-b5a1-4517-8514-cc514038d5f1 | App\Truck\Events\USDOTAssigned | {'usdot':1234567} | 2018-05-10 19:30:00 |
| 10 | App\Carrier\Aggregates\Carrier | b61cda06-90b1-4c0c-8670-8047fd83d1d8 | App\Carrier\Events\TruckAdded | {'truck':'460b0c4e-b5a1-4517-8514-cc514038d5f1'} | 2018-05-10 19:30:00 |
|  |  |  |  |  |  |
| 11 | App\Truck\Aggregates\Truck | 460b0c4e-b5a1-4517-8514-cc514038d5f1 | App\Truck\Events\AccidentReported | {'accident':{'date':'2018-04-01'}} | 2018-04-01 00:00:00 |

#### Versioning Events

Versioning events is an important topic but is also quite complicated for this basic intro. Adding a `version` column and using a date or integer for the value is often employed. Alternatively you could just create new events under different names or namespaces and/or run a migration on your data set to transform the old events to the newly defined events so long as it preserves the data. Event sourcing is intended to be used with well-defined domain-level events and aggregate models. Consider planning out your events before implementing them so as to avoid the need to version your events in the first place.

#### Advanced Tips

- If `payload` values need to be normalized out of the table then they should be created on related tables where this table simply stores the relationship references to the aggregate and the foreign payload data.
- If `payload` values need to be searched when sourcing events out of the table then `JSON_EXTRACT` could be used (available in MariaDB 10.2).
- If `aggregate` and `uuid` need to be normalized then a separate `event_aggregates` table could be created which stores `id`, `type`, and `uuid` respectively and the `events` table be updated to refer to `aggregate_id`.
- If `type` needs to be normalized then an `event_types` table could be created which stores `id` and `value` respectively and the `events` table be updated to refer to `type_id`.
- The `created_at` column may need to be converted to a millisecond precise timestamp depending on needed granularity of the time ordering of events.
- Finally in a large event store that involves distribution of events across multiple databases, it may be necessary to use a UUID for the event `id` itself.

## Snapshots Table (Snapshot)

This is a specialized table that essentially squashes the `events` table records into a single base event. Snapshots represent the sum of all changes to an aggregate between any two events in stream of events. The purpose of snapshots is only to improve the performance of the event store by streaming fewer events per aggregate.

| id | aggregate | uuid | event | payload | created_at |
| --- | --- | --- | --- | --- | --- |
| 1 | App\Truck\Aggregates\Truck | 460b0c4e-b5a1-4517-8514-cc514038d5f1 | 6 | {'uuid':'460b0c4e-b5a1-4517-8514-cc514038d5f1','vin':'12345678901234567','unit':1,'color':'red','tag':{'number':'XYZ 123','expires':'2019-03-31','region':'US-TX'}} | 2018-04-15 14:23:00 |
|  |  |  |  |  |  |
| 2 | App\Truck\Aggregates\Truck | 460b0c4e-b5a1-4517-8514-cc514038d5f1 | 11 | {'uuid':'460b0c4e-b5a1-4517-8514-cc514038d5f1','vin':'12345678901234567','unit':1,'color':'red','tag':{'number':'XYZ 123','expires':'2019-03-31','region':'US-TX'},'accident':{'date':'2018-04-01'}} | 2018-04-15 14:23:00 |

### Examples

The following are example analogies of what a snapshot is:

#### Taking a Trip

- **Aggregate**: the trip including origin location
- **Event**: a driver's mileage log recorded every hour
    - **Payload**: the current direction and difference in mileage reading relative to the last log
- **Projection**: the vehicles current position, speed, and total distance traveled
- **Snapshot**: the current direction and difference in mileage reading relative to the origin or last snapshot

The tricky bit with this example is that the aggregate has a fixed original location recorded as a latitude and longitude. This initial event should be recorded when the vehicle is started so as to establish the initial direction (e.g.: 30 deg west of north) and the initial mileage (e.g. 1500 odometer) reading. This first event will represent the state when the trip was started. All other events will be smaller changes offset from this initial event. Snapshots will be the delta of change between the initial event following the path created by the stream of mileage logging events. A projection taken at the same time as the snapshot would be a translation of the original location by the direction and mileage described by the snapshot. This would give the current location and the average trip speed and the snapshot would already contain the total distance traveled.

Another caveat is that while it is possible to take a snapshot to get the average speed of the trip and begin using that as the basis for future readings, to accurately represent congestion or speed along the way, only the events taken individually can project that level of detail. A specialized projection could be created while streaming through each of the events to save the average speed at the time of each trip log (event). Then you could utilize that added speed log (event) to better visualize the speed along the way. Collate the events with data about the speed limits along the way and/or other traveler's speeds and you can identify where a driver was speeding in excess of the speed limit or pinpoint the location of an accident.

Increasing the frequency of the mileage logging from every hour to every minute provides increased resolution at the expense of increased processing time. Creating persistent snapshots every hour however would allow for fine tuning the data processing by rolling up minute-level events (60 per hour) into a event-like hour-level snapshots (1 per hour).

#### Squashing Commits

- **Aggregate**: a feature branch
- **Event**: a commit against the feature branch
    - **Payload**: the changes to the files in the commit
- **Projection**: the source code in a releases' archive
- **Snapshot**: the single commit after squashing all commits in a merge request

Strictly speaking, in the world of version control, a squashed commit is a new commit which translated in this analogy would mean that a snapshot is a new event. While it is possible to insert a snapshot type event into the event store, it is a better practice to instead insert the snapshot into a related table and preserve the original event stream uninterrupted. Also unlike in version control where the commits that are squashed are deleted, usually the events are retained for future use.

#### Advanced Tips

- The table includes a reference to last the `event_id` that was used to generate the snapshot: use the timestamp of this event to time-order the snapshots.
- If a new event is inserted into the `events` table at a time before an existing snapshot, all existing snapshots that are at a time after the newly inserted event should be invalidated (deleted).
- Snapshots can be automatically generated using an aggregate observer that inserts a new snapshot every nth number of events relating to the aggregate are created or at a regular interval.

# Trucks Table (Projection)

This is your typical read model table with the VIN decoded and stored as denormalized (and cached) values including the `make`, `model`, and `year`. Other columns like `region` and date values has been processed by converting the event data to more normalized values. The purpose of a projection is to provide a data store that is more ideally suited for read performance than for write performance so denormalization is usually preferred.

| id | uuid | vin | make | model | year | unit | color | lpn | region | expires_at | created_at | updated_at |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| 1 | 460b0c4e-b5a1-4517-8514-cc514038d5f1 | 12345678901234567 | Ford | F-150 | 2019 | 1 | blue | XYZ 123 | US-TX | 2019-03-31 23:59:59 | 2018-03-31 09:00:00 | 2019-03-31 09:00:00 |
| 2 | 460b0c4e-b5a1-4517-8514-cc514038d5f1 | 12345678901234567 | Ford | F-150 | 2019 | 1 | red | XYZ 123 | US-TX | 2019-03-31 23:59:59 | 2018-03-31 09:00:00 | 2019-04-15 14:23:00 |
| 3 | 460b0c4e-b5a1-4517-8514-cc514038d5f1 | 12345678901234567 | Ford | F-150 | 2019 | 2 | red | XYZ 123 | US-TX | 2019-03-31 23:59:59 | 2018-03-31 09:00:00 | 2019-05-10 18:03:00 |

### Advanced Tips

- A projection table is a disposable table that can be recreated at any time from the event store.
- The same event stream can be projected to multiple projection tables – each table suited for its specific read model's use case.
- While projections can be stored in the same database or on the same server as the event store there is no reason that the projection tables cannot be stored separately, relying upon eventual consistency.
- It is sometimes more efficient to insert a super seeding projection by limiting the query to the most recent projection for the aggregate. Other times it is more convenient to run an update statement to replace the values of the existing projection. Still other times it is useful to delete the entire table and re-project it. Not all projections have to project the same way.

# Carriers Table (Projection)

This is your typical read model table with the added benefit that you have some denormalized values like the number of `drivers` and `trucks` on the table. This eliminates the need to run sub select statements when querying for carriers. When creating a projection, the table's data can be derived from the events in the stream, from external data sources related to those events' data (e.g.: external API call to verify and return registration information concerning the USDOT number) or from other projections (e.g.: query the count of trucks grouped by USDOT number) or event streams (e.g.: counting the maximum number of unique trucks the carrier has ever had by counting the number of `App\Truck\Events\USDOTAssigned` events relating to the carrier's USDOT).

| id | uuid | usdot | name | drivers | trucks | interstate | active | created_at | updated_at |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| 1 | b61cda06-90b1-4c0c-8670-8047fd83d1d8 | 1234567 | Acme Transport Co. | 3 | 0 | true | true | 2018-05-10 19:00:00 | 2018-05-10 19:00:00 |
| 2 | b61cda06-90b1-4c0c-8670-8047fd83d1d8 | 1234567 | Acme Transport Co. | 3 | 1 | true | true | 2018-05-10 19:00:00 | 2018-05-10 19:30:00 |

# Fleets Table (Projection)

This is just a many to many table between trucks and carriers and has collated on it all the joined values as columns. It therefore could have been created with simply `truck_id` and `carrier_id` as columns and called `carriers_trucks` table. This would then necessitate the need to combine the data together at request time for each request instead of only once at projection time. The goal of a projection is to be able to re-run the projection as often as the event store is modified so that projections are eventually consistent with their aggregates current state.

| id | truck | carrier | vin | make | model | year | unit | color | lpn | region | usdot | name | drivers | trucks | interstate | active | expires_at | created_at | updated_at |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| 1 | 460b0c4e-b5a1-4517-8514-cc514038d5f1 | b61cda06-90b1-4c0c-8670-8047fd83d1d8 | 12345678901234567 | Ford | F-150 | 2019 | 2 | red | XYZ 123 | US-TX | 1234567 | Acme Transport Co. | 3 | 1 | true | true | 2019-03-31 23:59:59 | 2018-03-31 09:00:00 | 2018-05-10 19:30:00 |

## License

Copyright (c) 2018 [Artisans Collaborative](http://artisanscollaborative.com). This example code is licensed under the MIT License.
