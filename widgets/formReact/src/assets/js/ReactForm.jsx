import React, { useState } from 'react';
import "../../../../../assets/css/App.css"

const ReactForm = () => {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    message: ''
  });

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    console.log('Form submitted:', formData);

    try {
      const response = await fetch('/wp-admin/admin-ajax.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
          action: 'submit_react_form',
          ...formData
        })
      });

      if (response.ok) {
        const result = await response.json();
        console.log('Server response:', result);
        // Handle successful submission (e.g., show a success message)
      } else {
        console.error('Server error:', response.statusText);
        // Handle error (e.g., show an error message)
      }
    } catch (error) {
      console.error('Fetch error:', error);
      // Handle network errors
    }
  };

  return (
    <form onSubmit={handleSubmit} className="max-w-md mx-auto mt-8 bg-black">
      <input
        type="text"
        name="name"
        value={formData.name}
        onChange={handleChange}
        placeholder="Name"
        className="w-full px-3 py-2 mb-4 border rounded-md"
        required
      />
      <input
        type="email"
        name="email"
        value={formData.email}
        onChange={handleChange}
        placeholder="Email"
        className="w-full px-3 py-2 mb-4 border rounded-md"
        required
      />
      <textarea
        name="message"
        value={formData.message}
        onChange={handleChange}
        placeholder="Message"
        className="w-full px-3 py-2 mb-4 border rounded-md"
        required
      ></textarea>
      <button type="submit" className="w-full px-3 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600">
        Submit
      </button>
    </form>
  );
};

export default ReactForm;