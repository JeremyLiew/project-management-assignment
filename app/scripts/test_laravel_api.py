import requests

# Base URL of your Laravel API
BASE_URL = 'http://your-laravel-app.com/api'

# Get all projects
response = requests.get(f'{BASE_URL}/projects')
print("Get All Projects:")
print(response.json())

# Get a single project by ID
response = requests.get(f'{BASE_URL}/projects/1')
print("\nGet Project by ID:")
print(response.json())

# Create a new project
new_project = {
    'name': 'New Project',
    'description': 'Description of new project'
}
response = requests.post(f'{BASE_URL}/projects', json=new_project)
print("\nCreate New Project:")
print(response.json())

# Update an existing project
updated_project = {
    'name': 'Updated Project Name',
    'description': 'Updated project description'
}
response = requests.put(f'{BASE_URL}/projects/1', json=updated_project)
print("\nUpdate Project:")
print(response.json())

# Delete a project
response = requests.delete(f'{BASE_URL}/projects/1')
print("\nDelete Project:")
print(response.json())
