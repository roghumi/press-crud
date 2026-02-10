# LaraPress Resource Definition YAML HOWTO

This guide explains how to create complete resource definition YAML files for the LaraPress CRUD framework.

## **1. Basic File Structure**

A resource definition YAML file represents a single database table and defines how the CRUD system should generate API endpoints, models, and validation rules.

```yaml
doc: Document description for this resource.
resource: FullyQualifiedClassName
provider: FullyQualifiedProviderClassName
namespace: App\Generated\Namespace
output: app/Generated/Api/ResourceName
timestamp: true
softDeletes: true
factory:
  namespace: App\Generated\Factories
  output: app/Generated/Factory/ResourceFactory.php
verbs:
  - verb: CreateVerb
    comp: true
columns:
  - name: column_name
    doc: Column documentation
    type: string
    sort: true
    input: true
    filters: [in]
    rules: [unique]
    default: "default_value"
    nullable: true
relations:
  - name: relation_name
    doc: Relation documentation
    type: HasMany
    props: []
    provider: user.provider
    class: Post
```

## **2. Top-Level Options**

### **`doc`**
- **Description**: Human-readable description of the resource for documentation purposes.
- **Type**: String
- **Example**: `# User management API`

### **`resource`**
- **Description**: Fully qualified class name of the Eloquent model this resource represents.
- **Type**: String (class name)
- **Example**: `User`

### **`provider`**
- **Description**: Fully qualified class name of the resource provider (custom logic layer).
- **Type**: String (class name)
- **Example**: `UserResourceProvider`

### **`namespace`**
- **Description**: Namespace for all auto-generated classes (resource model, crud providers and composites, etc.).
- **Type**: String (valid PHP namespace)
- **Example**: `App\CRUD\User`

### **`timestamp`**
- **Description**: Whether to auto-manage Laravel's timestamp columns (`created_at`, `updated_at`).
- **Type**: `true` or array of column names
- **Valid values**: `true`, `['created_at', 'updated_at']`
- **Example**: `true`

### **`softDeletes`**
- **Description**: Whether soft delete column is used (enables soft delete functionality).
- **Type**: `true` or string (column name, default is `'deleted_at'`)
- **Valid values**: `true`, `'deleted_at'` (custom column name)
- **Example**: `true`

### **`output`**
- **Description**: Output folder path for generated files.
- **Type**: String (relative path)
- **Example**: `app/CRUD/Users`

### **`factory`**
- **Description**: Factory configuration for database seeding or testing.
- **Type**: `false` or object with `namespace` and `output` properties
- **Valid values**:
  - `false` (turn off factory generation)
  - `{namespace: string, output: string}` (enable factory generation)
    - `namespace`: Namespace for generated factory class.
    - `outout`: Output directory to generate factory class into. 
- **Example**:
```yaml
factory:
  namespace: App\Generated\Factories
  output: app/Generated/Factories
```

## **3. Verb Configuration (`verbs`)**

Configures how different HTTP verbs behave for your resource.

### **`verb`**
- **Description**: The verb class to be used (e.g., CreateVerb, UpdateVerb).
- **Type**: String
- **Valid values**:
  - `CreateVerb`
  - `UpdateVerb`
  - `DeleteVerb`
  - `ListVerb`
  - `ShowVerb`
  - `QueryVerb`
  - Or any custom verbs registered in your crud.php config file

### **`comp`**
- **Description**: Extra composite classes for the verb. If true, use standard composites.
- **Type**: `boolean` or `array`
- **Valid values**:
  - `true` (use default composites)
  - `['MyCustomClass', 'AnotherClass']` (specific composites)
- **Example**: `comp: ['Admin']`

## **4. Column Configuration (`columns`)**

Each database column mapped to a resource is defined with these settings.

### **`name`**
- **Description**: Name of the database column.
- **Type**: String (column name)

### **`doc`**
- **Description**: Documentation comment for the column.
- **Type**: String

### **`type`**
- **Description**: Database column type (used for validation).
- **Type**: String (typically `string`, `integer`, `boolean`, `text`, `date`, `datetime`)
- **Example**: `string`

### **`sort`**
- **Description**: If the column can be sorted in queries.
- **Type**: `true` or `false`
- **Valid values**: `true`, `false`

### **`input`**
- **Description**: Is this column allowed to accept values from external sources (if `false` Create and Update verbs wont read incoming API values for this column and instead expect an internal logic to provide the values for the verb).
- **Type**: `boolean`, `'auth_id'`, or object
- **Valid values**:
  - `true` (allow input)
  - `false` (exclude from input)
  - `'auth_id'` (use current authenticated user ID as input value)
  - Object with extra input options
- **Example**: `input: true`

### **`filters`**
- **Description**: Available filters for this column in queries.
- **Type**: Array of strings
- **Valid values**:
  - `'in'`
  - `'equals'`
  - `'between'`
  - `'contains'`
- **Example**: `filters: [in, equals]`

### **`rules`**
- **Description**: Validation rules for the input.
- **Type**: String or array of rule objects
- **Valid values**: 
  - String (e.g., `'required|string'`)
  - Array of objects with keys such as:
    - `unique` (with optional `update` param)
    - `clone` (to override rule for clone)
- **Example**:
```yaml
rules: 
  - unique: true
    update: 2
```

### **`default`**
- **Description**: Default value used when sanitizing inputs.
- **Type**: String, number, or null

### **`nullable`**
- **Description**: Whether this column can accept `null`.
- **Type**: `true` or `false`
- **Valid values**: `true`, `false`

## **5. Relation Configuration (`relations`)**

Defines relationship mappings for Eloquent models.

### **`name`**
- **Description**: Name of the relationship.
- **Type**: String

### **`doc`**
- **Description**: Documentation for the relationship.
- **Type**: String

### **`type`**
- **Description**: Type of Eloquent relationship.
- **Type**: String (matches keys in `DIC_RELATIONS`)
- **Valid values**:
  - `'BelongsTo'`
  - `'BelongsToMany'`
  - `'HasMany'`
  - `'HasOne'`
  - `'HasManyThrough'`
  - `'MorphTo'`
  - `'MorphOne'`
  - `'MorphMany'`

### **`props`**
- **Description**: Additional properties or modifiers for the relation.
- **Type**: Array of strings
- **Example**: `['withTrashed']`

### **`provider`**
- **Description**: Resource provider FQN.
- **Type**: String or alias
- **Valid values**:
  - Fully qualified provider class
  - `'user.provider'` (reference to system user provider)
- **Example**: `'user.provider'`

### **`class`**
- **Description**: Model class for relationship.
- **Type**: String or alias
- **Valid values**:
  - Fully qualified model class (e.g., `App\Models\Post`)
  - `'user.class'` (reference to system user class)
  - Base name (e.g., `Post`, assuming `App\Models\Post`)

---

## **6. Complete Example**

```yaml
doc: "User management API"
resource: App\Models\User
provider: App\Providers\UserResourceProvider
namespace: App\Generated\Users
timestamp: true
softDeletes: true
output: app/Generated/Api/Users
factory:
  namespace: App\Generated\Factories
  output: app/Generated/Factory/UserFactory.php
ignoreFiles:
  - UserFactory.php
verbs:
  - verb: CreateVerb
    comp: [Admin]
  - verb: UpdateVerb
    comp: true
columns:
  - name: email
    doc: "User's email address"
    type: string
    sort: true
    input: true
    filters: [equals, contains]
    rules: [unique]
    nullable: false
  - name: active
    doc: "Is user active"
    type: boolean
    sort: true
    input: true
    filters: [equals]
    default: true
    nullable: false
relations:
  - name: posts
    doc: "User's created posts"
    type: HasMany
    props: [withTrashed]
    provider: user.provider
    class: Post
  - name: roles
    doc: "User roles"
    type: BelongsToMany
    provider: user.provider
    class: Role
```

This resource definition will:
- Generate CRUD endpoints for users
- Handle timestamps and soft deletes
- Include proper validation rules
- Set up relationships with posts and roles
- Enable query filtering
- Skip factory file during generation