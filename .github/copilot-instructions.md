# OpenCart Development Rules

This repository is an OpenCart 3.x ecommerce project.

Architecture follows MVC-L pattern.

Controller locations:
catalog/controller/
admin/controller/

Model locations:
catalog/model/
admin/model/

View locations:
catalog/view/theme/
admin/view/template/

Language files:
catalog/language/
admin/language/

Routes follow:

index.php?route=folder/controller/method

Example:

index.php?route=test/company/eik

Controllers should:

- extend Controller
- validate request data
- return JSON for AJAX endpoints

Use:

$this->response->addHeader('Content-Type: application/json');
$this->response->setOutput(json_encode($json));

Models should:

- extend Model
- use $this->db->query()

Twig templates use syntax:

{{ variable }}

{% if condition %}

{% for item in items %}

JavaScript uses jQuery for AJAX requests.

AJAX calls should use:

$.ajax()

Security:

- validate tokens
- validate request method
- sanitize input

Forms should return validation errors as:

json.errors[field]