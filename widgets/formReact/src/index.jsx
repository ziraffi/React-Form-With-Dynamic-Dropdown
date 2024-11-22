import React from 'react';
import ReactDOM from 'react-dom';
// import ReactForm from './assets/js/ReactForm';
import IndianPincodesAdv from './assets/components/IndianPincodesAdv';

document.addEventListener('DOMContentLoaded', () => {
  const reactRoot = document.getElementById('react-form-root');
  if (reactRoot) {
    ReactDOM.render(<IndianPincodesAdv />, reactRoot);
  }
});