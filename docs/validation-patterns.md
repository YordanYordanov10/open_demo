
---

# 📄 `docs/validation-patterns.md`

```md
# OpenCart Validation Patterns

Validation should be done in controllers.

## Basic Validation Example

```php
if (!$this->request->post['company']) {
    $json['errors']['company'] = 'Company name is required';
}

Request Method Validation
if ($this->request->server['REQUEST_METHOD'] != 'POST') {
    $json['error'] = 'Invalid request method';
}

Token Validation
if (!isset($this->request->get['token'])) {
    $json['error'] = 'Invalid token';
}

Required Field Validation
if (utf8_strlen($this->request->post['company']) < 2) {
    $json['errors']['company'] = 'Company name must be at least 2 characters';
}