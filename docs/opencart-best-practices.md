# OpenCart Best Practices

This project follows OpenCart 3.x development standards.

## Architecture

OpenCart uses MVC-L architecture.

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

---

# Controllers

Controllers should:

- extend Controller
- validate request input
- load models
- pass data to views
- return JSON for AJAX endpoints

Example:

```php
class ControllerTestExample extends Controller {

    public function index() {

        $data = [];

        $this->response->setOutput(
            $this->load->view('test/example', $data)
        );

    }

}