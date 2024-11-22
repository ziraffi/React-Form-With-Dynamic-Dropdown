(function ($) {
    // Constants for better readability and maintainability
    const STORAGE_KEY = 'darkMode';
    const COOKIE_EXPIRE_DAYS = 1;
  
    // Function to set a cookie with an expiry date
    function setCookie(name, value, days) {
      let expires = "";
      if (days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
      }
      document.cookie = `<span class="math-inline">\{name\}\=</span>{value}${expires}; path=/`;
    }
  
    // Function to get a cookie value
    function getCookie(name) {
      const nameEQ = `${name}=`;
      const ca = document.cookie.split(';');
      for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
      }
      return null;
    }
  
    // Function to load initial dark mode state from cookie or localStorage, handling potential parsing errors
    function getInitialDarkMode() {
      let isDarkMode = false;
      try {
        const cookieValue = getCookie(STORAGE_KEY);
        isDarkMode = cookieValue === 'true';
      } catch (error) {
        console.warn('Error parsing cookie value for dark mode:', error);
      }
  
      try {
        const localStorageValue = localStorage.getItem(STORAGE_KEY);
        isDarkMode = localStorageValue === 'true';
      } catch (error) {
        console.warn('Error parsing localStorage value for dark mode:', error);
      }
  
      return isDarkMode;
    }
  
    // Map of dark mode colors with descriptive names
    const darkModeColors = {
        '--e-global-color-primary': '#fff',
        '--e-global-color-secondary': '#fff',
        '--e-global-color-text': '#fff',
        '--e-global-color-accent': '#fff',
        '--e-global-color-665b1bd': '#F9C059',
        '--e-global-color-77e4e83': '#7A7A7A',
        '--e-global-color-5414059': '#1f1f1f',
        '--e-global-color-ed4a2fc': '#fff'
    };

    // Map of light mode colors with descriptive names
    const lightModeColors = {
        '--e-global-color-primary': '',
        '--e-global-color-secondary': '',
        '--e-global-color-text': '',
        '--e-global-color-accent': '',
        '--e-global-color-665b1bd': '',
        '--e-global-color-77e4e83': '',
        '--e-global-color-5414059': '',
        '--e-global-color-ed4a2fc': ''
    };
  
    // Initial dark mode state based on reliable storage
    let isDarkMode = getInitialDarkMode();
  
    // Apply the correct mode on page load
    updateMode();
  
    // Toggle dark mode on button click
    $("#dark-mode-toggle").on("click", toggleDarkMode);
  
    // Function to update mode based on the current state
    function updateMode() {
      if (isDarkMode) {
        applyDarkMode();
      } else {
        applyLightMode();
      }
    }
  
    // Function to apply dark mode settings with proper error handling
    function applyDarkMode() {
      try {
        $("body").addClass("dark-mode");
        $("#dark-mode-toggle").addClass("active");
        setGlobalColors(darkModeColors);
        updateIcons('white'); // Update icons to white
      } catch (error) {
        console.error('Error applying dark mode:', error);
      }
    }
  
    // Function to apply light mode settings with proper error handling
    function applyLightMode() {
      try {
        $("body").removeClass("dark-mode");
        $("#dark-mode-toggle").removeClass("active");
        setGlobalColors(lightModeColors);
        updateIcons('black'); // Update icons to black
      } catch (error) {
        console.error('Error applying light mode:', error);
      }
    }
  
    // Function to toggle dark mode state and save to reliable storage
    function toggleDarkMode() {
      isDarkMode = !isDarkMode;
      // Save to both cookie and localStorage for redundancy
    setCookie(STORAGE_KEY, isDarkMode, COOKIE_EXPIRE_DAYS);
    localStorage.setItem(STORAGE_KEY, isDarkMode);

    // Send the preference to the server via AJAX
    $.ajax({
      url: darkModeData.ajaxurl,
      method: 'POST',
      data: {
        action: 'handle_toggle_dark_mode',
        nonce: darkModeData.nonce,
        dark_mode: isDarkMode
      },
      success: function () {
        console.log('Dark mode preference saved:', isDarkMode);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        const responseText = jqXHR.responseText ? JSON.parse(jqXHR.responseText) : {};
        console.error('Error saving dark mode preference:', textStatus, errorThrown, responseText);
      }
    });

    updateMode();
  }

  // Function to set global color variables with descriptive names
  function setGlobalColors(colors) {
    const kitElements = document.querySelectorAll('.elementor-kit-7');
    kitElements.forEach((element) => {
        for (const [key, value] of Object.entries(colors)) {
            element.style.setProperty(key, value);
        }
    });
  }

  // Function to update icons' color based on the mode
  function updateIcons(color) {
    $('.elementor-icon').css('fill', color);
  }

})(jQuery);

