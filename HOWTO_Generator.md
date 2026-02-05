# LaraPress Resource Definition Documentation

This document provides detailed documentation for each property in the LaraPress resource definition YAML files.

## Resource Metadata Properties

### `doc`
- **Type**: String
- **Description**: Document description for this resource
- **Usage**: Provides human-readable documentation about the resource

### `resource`
- **Type**: String
- **Description**: Class name of the resource
- **Usage**: Defines the main resource class name that will be generated

### `provider`
- **Type**: String
- **Description**: Class name of the resource provider
- **Usage**: Specifies the provider class that handles resource operations

### `namespace`
- **Type**: String
- **Description**: Namespace for all generated classes
- **Usage**: Sets the base namespace for all auto-generated classes related to this resource

### `timestamp`
- **Type**: Boolean or Array of Strings
- **Description**: Enable/disable timestamp columns (created_at, updated_at)
- **Usage**: 
  - `true`: Enable both created_at and updated_at columns
  - `false`: Disable timestamp columns
  - Array: Specify which timestamp columns to include (e.g., `['created_at']`)

### `softDeletes`
- **Type**: Boolean or String
- **Description**: Enable/disable soft deletes (deleted_at column)
- **Usage**: 
  - `true`: Enable soft delete column with default name "deleted_at"
  - `false`: Disable soft deletes
  - String: Specify custom soft delete column name

### `output`
- **Type**: String
- **Description**: Folder path for generated files
- **Usage**: Defines the directory where all auto-generated files will be placed

### `factory`
- **Type**: Object or Boolean
- **Description**: Factory configuration for database generation
- **Properties**:
  - `namespace`: Namespace of generated factory class
  - `output`: Path of generated factory class file
- **Usage**: 
  - `true`: Enable factory generation with default settings
  - `false`: Disable factory generation
  - Object: Provide custom factory configuration

### `ignoreFiles`
- **Type**: Array of Strings
- **Description**: List of files to skip when generating
- **Usage**: Prevents generation of specific file types (e.g., `['migrations', 'resource']`)

## Verb Configuration Properties

### `verbs`
- **Type**: Array of Objects
- **Description**: List of verb objects that define available operations
- **Object Properties**:
  - `verb`: Class name of desired verb (create, update, query, etc.)
  - `comp`: Boolean or list of extra composite class names

## Column Definition Properties

### `columns`
- **Type**: Array of Objects
- **Description**: Array of column objects defining database table fields
- **Object Properties**:
  - `name`: Column in database table
  - `doc`: Column document description
  - `type`: Column type in database table
  - `sort`: Is column sortable
  - `input`: Input object (or false or 'auth_id')
  - `filters`: List of filters for column
  - `rules`: String of rules or list of rule objects
  - `default`: Default value for column when sanitizing inputs
  - `nullable`: Is column nullable and can column be updated to null

### Column-specific Properties:

#### `name`
- **Type**: String
- **Description**: Database column name

#### `doc`
- **Type**: String
- **Description**: Column document description

#### `type`
- **Type**: String
- **Description**: Column type in database table
- **Usage**: Specifies data type (string, integer, boolean, etc.)

#### `sort`
- **Type**: Boolean
- **Description**: Is column sortable
- **Usage**: Determines whether this column can be used for sorting in queries

#### `input`
- **Type**: Object or Boolean or String
- **Description**: Input handling configuration
- **Usage**:
  - `false`: Field should not be included in input handling
  - `'auth_id'`: Automatically populated with authenticated user ID
  - Object: Specific input configuration

#### `filters`
- **Type**: Array of Strings
- **Description**: List of filters for column
- **Usage**: Defines available filtering options for this column in queries

#### `rules`
- **Type**: String or Array of Objects
- **Description**: Validation rules for column
- **Usage**:
  - String: Simple validation rules (e.g., `"required|string|min:3"`)
  - Object: Complex rules with special handling:
    - `[rule key name]`: One of 'unique' or other validation rules
    - `update`: Exclude arguments from unique rule during updates
    - `clone`: Update rule name with clone rule name in clone operations

#### `default`
- **Type**: Any
- **Description**: Default value for column when sanitizing inputs
- **Usage**: Sets default value for column during input sanitization

#### `nullable`
- **Type**: Boolean
- **Description**: Is column nullable and can column be updated to null
- **Usage**: Determines whether column can accept NULL values

## Relation Definition Properties

### `relations`
- **Type**: Array of Objects
- **Description**: Array of relation objects defining model relationships
- **Object Properties**:
  - `name`: Relation name
  - `doc`: Relation document description
  - `type`: Relation type used to determine relation class
  - `props`: List of props to be passed to the relation class
  - `provider`: FQN of provider class or one of 'user.provider'
  - `class`: FQN of model class or class base name of a resource or 'user.class'

### Relation-specific Properties:

#### `name`
- **Type**: String
- **Description**: Relation name

#### `doc`
- **Type**: String
- **Description**: Relation document description

#### `type`
- **Type**: String
- **Description**: Relation type used to determine relation class
- **Usage**: Specifies type of relation (BelongsTo, HasMany, etc.)
- **Reference**: See Services/Generator/Constants and RelationsDictionary

#### `props`
- **Type**: Array of Strings
- **Description**: List of props to be passed to the relation class
- **Usage**: Additional parameters needed to configure the relation

#### `provider`
- **Type**: String
- **Description**: FQN of provider class or one of 'user.provider'
- **Usage**: Specifies the provider for this relation

#### `class`
- **Type**: String
- **Description**: FQN of model class or class base name of a resource or 'user.class'
- **Usage**: Specifies the target class for the relationship