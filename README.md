# Magento 2 Order Comment Module

## Description

The `Pg_OrderComment` module for Magento 2 allows to add a special comment to orders after order is placed. 

This comment is configurable per store view, website, or globally. 

After order is placed the special comment is stored in a new column called `comment` in the `sales_order` table.

## Features

- Adds a configurable special comment to orders which can be set at the global, website, or store view level.
- Uses RabbitMQ to asynchronously process the addition of the comment to orders after order is placed.

## Installation

1. Copy the module files to the `app/code/Pg/OrderComment` directory.
2. Run `bin/magento module:enable Pg_OrderComment` to enable the module.
3. Run `bin/magento setup:upgrade` to install the module.
4. Configure the special comment text under `Stores > Configuration > Sales > Sales > Order Comment`.
5. Ensure RabbitMQ is correctly configured and running.
6. If it's not run automatically, you can run `bin/magento queue:consumers:start pg.order.consumer`.

## Technical Details

### Configuration
In `system.xml` the field for the special comment text is configured.

In `config.xml` there is default value assigned to the new configuration field.

The interface `ConfigInterface` and class `Config` are implemented to manage the retrieval of the configuration value for the special comment.

### Database

In `db_schema.xml` a new column `comment` was added to `sales_order` table.

### Extension Attributes

In order to add custom attribute to Order the Extension Attributes is used.

In `extension_attribute.xml` the attribute is specified.

The interface `OrderCommentInterface` is defined to declare getter and setter methods for the attribute and `Order Comment` class implements the interface and provide actual logic for getting and setting data.

The plugins `OrderGetPlugin` and `OrderSavePlugin` are used to read and write the `comment` extension attribute during the loading and saving of Order entities ensuring the attribute is populated and persisted correctly.

### Event and Observer

In order to add order id to queue, after order is placed the `sales_model_service_quote_submit_success` event is used (as `sales_order_place_after` event is triggered before order is saved in `sales_order` table, so the order has no ID assigned when event is triggered).

In `events.xml` it is specified which event the module observes. 

The `AddOrderIdToQueue` observer is handling the event and adds the order ID to the RabbitMQ queue.


### RabbitMQ

In order to configure RabbitMQ following filles were added: `communication.xml`, `queue_consumer.xml`, `queue_publisher.xml`, `queue_topology.xml`. The files define the exchange, queue and binding.

The queue name is `pg.order.queue`, and binding is done using routing key `pg.order.place.after`.

The `OrderProcessor` class is processing the messages from the queue: 
- Loads the order by ID from the message.
- Gets the special comment from the configuration based on the store ID of the order.
- Sets the comment as an extension attribute to the order.
- Saves the order with the updated extension attribute.

The interface `OrderProcessorInterface` is implemented to follow best practices, but when it comes to queue_consumer.xml it's often a best practice to use concrete classes as it's often clearer and more flexible to use the concrete class directly in the XML.

### Unit Tests

To test critical functionality the Unit Tests are added.

