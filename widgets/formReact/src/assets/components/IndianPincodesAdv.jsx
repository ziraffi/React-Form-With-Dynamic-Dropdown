import {
  useEffect,
  useMemo,
  useState,
  useCallback,
  Suspense,
  useRef,
} from "react";
import DOMPurify from "dompurify";
import ResubmitModal from "./ResubmitModal";

function IndianPincodesAdv() {
  const [firstName, setFirstName] = useState("");
  const [lastName, setLastName] = useState("");
  const [email, setEmail] = useState("");
  const [phoneNumber, setPhoneNumber] = useState("");
  const [addressLine, setAddressLine] = useState("");
  const [landmark, setLandmark] = useState("");
  const [errors, setErrors] = useState({});

  const [selectedCountry, setSelectedCountry] = useState(null);
  const [selectedState, setSelectedState] = useState(null);
  const [selectedCity, setSelectedCity] = useState(null);
  const [selectedDistrict, setSelectedDistrict] = useState(null);
  const [selectedTaluk, setSelectedTaluk] = useState(null);
  const [selectedBranchOffice, setSelectedBranchOffice] = useState(null);
  const [selectedPincode, setSelectedPincode] = useState(null);

  const [countrySearch, setCountrySearch] = useState("");
  const [stateSearch, setStateSearch] = useState("");
  const [districtSearch, setDistrictSearch] = useState("");
  const [talukSearch, setTalukSearch] = useState("");
  const [branchOfficeSearch, setBranchOfficeSearch] = useState("");
  const [pincodeSearch, setPincodeSearch] = useState("");

  const [countries, setCountries] = useState([]);
  const [states, setStates] = useState([]);
  const [cities, setCities] = useState([]);
  const [indianData, setIndianData] = useState([]);
  const [citySearch, setCitySearch] = useState("");
  const [allStates, setAllStates] = useState([]);
  const [allCities, setAllCities] = useState([]);

  const [isSearchingCountry, setIsSearchingCountry] = useState(false);
  const [isSearchingState, setIsSearchingState] = useState(false);
  const [isSearchingCity, setIsSearchingCity] = useState(false);
  const [isSearchingDistrict, setIsSearchingDistrict] = useState(false);
  const [isSearchingTaluk, setIsSearchingTaluk] = useState(false);
  const [isSearchingOffice, setIsSearchingOffice] = useState(false);

  const [isIndianPlaces, setIsIndianPlaces] = useState(false);
  const [optimizedIndianData, setOptimizedIndianData] = useState({});

  const [isLoadingCountries, setIsLoadingCountries] = useState(false);
  const [isLoadingCities, setIsLoadingCities] = useState(false);

  const countryDropdownRef = useRef(null);
  const stateDropdownRef = useRef(null);
  const cityDropdownRef = useRef(null);
  const districtDropdownRef = useRef(null);
  const talukDropdownRef = useRef(null);
  const branchOfficeDropdownRef = useRef(null);

  const [submissionStatus, setSubmissionStatus] = useState(null);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [showResubmitModal, setShowResubmitModal] = useState(false);
  const [formData, setFormData] = useState([]);

  useEffect(() => {
    const handleClickOutside = (event) => {
      switch (true) {
        case countryDropdownRef.current &&
          !countryDropdownRef.current.contains(event.target):
          setIsSearchingCountry(false);
          break;
        case stateDropdownRef.current &&
          !stateDropdownRef.current.contains(event.target):
          setIsSearchingState(false);
          break;
        case districtDropdownRef.current &&
          !districtDropdownRef.current.contains(event.target):
          setIsSearchingDistrict(false);
          break;
        case talukDropdownRef.current &&
          !talukDropdownRef.current.contains(event.target):
          setIsSearchingTaluk(false);
          break;
        case branchOfficeDropdownRef.current &&
          !branchOfficeDropdownRef.current.contains(event.target):
          setIsSearchingOffice(false);
          break;
        case cityDropdownRef.current &&
          !cityDropdownRef.current.contains(event.target):
          setIsSearchingCity(false);
          break;
        default:
          break;
      }
    };

    document.addEventListener("mousedown", handleClickOutside);
    return () => {
      document.removeEventListener("mousedown", handleClickOutside);
    };
  }, []);

  useEffect(() => {
    const fetchData = async () => {
      setIsLoadingCountries(true);
      try {
        const [
          countriesResponse,
          statesResponse,
          citiesResponse,
          indianDataResponse,
        ] = await Promise.all([
          fetch(
            "https://raw.githubusercontent.com/dr5hn/countries-states-cities-database/refs/heads/master/json/countries.json"
          ),
          fetch(
            "https://raw.githubusercontent.com/dr5hn/countries-states-cities-database/refs/heads/master/json/states.json"
          ),
          fetch(
            "https://raw.githubusercontent.com/dr5hn/countries-states-cities-database/refs/heads/master/json/cities.json"
          ),
          fetch(window.reactFormData.jsonUrl),
        ]);

        const countriesData = await countriesResponse.json();
        const statesData = await statesResponse.json();
        const citiesData = await citiesResponse.json();
        const indianData = await indianDataResponse.json();

        setCountries(
          countriesData.map((country) => ({
            id: country.id,
            name: country.name,
          }))
        );
        setAllStates(statesData);
        setAllCities(citiesData);

        // Preprocess indianData
        const optimizedData = indianData.reduce((acc, item) => {
          const stateName = item.stateName
            .toLowerCase()
            .replace(/\./g, "")
            .trim();
          const districtName = item.districtName.trim().replace(/&/g, "and");
          const talukName = item.taluk.trim();

          if (!acc[stateName]) {
            acc[stateName] = {
              id: `state_${Object.keys(acc).length + 1}`,
              districts: {},
            };
          }

          if (!acc[stateName].districts[districtName]) {
            acc[stateName].districts[districtName] = {
              id: `district_${
                Object.keys(acc[stateName].districts).length + 1
              }`,
              taluks: {},
              offices: [],
            };
          }

          if (!acc[stateName].districts[districtName].taluks[talukName]) {
            acc[stateName].districts[districtName].taluks[talukName] = {
              id: `taluk_${
                Object.keys(acc[stateName].districts[districtName].taluks)
                  .length + 1
              }`,
            };
          }

          acc[stateName].districts[districtName].offices.push({
            id: `office_${
              acc[stateName].districts[districtName].offices.length + 1
            }`,
            officeName: item.officeName,
            pincode: item.pincode,
            taluk: talukName,
          });

          return acc;
        }, {});

        setIndianData(indianData);
        setOptimizedIndianData(optimizedData);
        // console.log(
        //   "Data loaded - Countries:",
        //   countriesData.length,
        //   "No.States:",
        //   statesData.length,
        //   "No.Cities:",
        //   citiesData.length,
        //   "No.Indian Data:",
        //   indianData.length,
        //   "No.Optimized data:",
        //   optimizedData.length,
        //   "States:",
        //   statesData,
        //   "Cities loaded:",
        //   citiesData
        // );
      } catch (error) {
        console.error("Error fetching data:", error);
      }
    };

    fetchData().finally(() => setIsLoadingCountries(false));
  }, []);

  // In the useEffect for States
  useEffect(() => {
    if (selectedCountry) {
      if (selectedCountry.id == 101) {
        setIsIndianPlaces(true);
        const indianStates = Object.keys(optimizedIndianData).map(
          (stateName, index) => ({
            state_name: stateName,
            state_id: `IN_${index + 1}`,
          })
        );
        setStates(indianStates);
      } else {
        setIsIndianPlaces(false);
        const filteredStates = allStates
          .filter((state) => state.country_id === parseInt(selectedCountry.id))
          .map((state) => ({
            state_name: state.name,
            state_id: state.id,
          }));
        setStates(filteredStates);
      }
    } else {
      setStates([]);
    }
  }, [selectedCountry, allStates, optimizedIndianData]);
  // In the useEffect for cities
  useEffect(() => {
    if (selectedCountry && selectedState) {
      setIsLoadingCities(true);
      if (isIndianPlaces) {
        setIsLoadingCities(false);
      } else {
        const filteredCities = allCities
          .filter(
            (city) =>
              city.country_id === parseInt(selectedCountry.id) &&
              city.state_id === parseInt(selectedState.state_id)
          )
          .map((city) => ({
            city_name: city.name,
            city_id: city.id,
          }));
        setCities(filteredCities);
        setIsLoadingCities(false);
      }
    } else {
      setCities([]);
    }
  }, [selectedCountry, selectedState, allCities, isIndianPlaces]);

  const filteredCountries = useMemo(() => {
    return countries.filter((country) =>
      country.name.toLowerCase().startsWith(countrySearch.toLowerCase())
    );
  }, [countries, countrySearch]);

  const filteredStates = useMemo(() => {
    return states.filter((state) =>
      state.state_name.toLowerCase().startsWith(stateSearch.toLowerCase())
    );
  }, [states, stateSearch]);

  const filteredCities = useMemo(() => {
    if (!cities || cities.length === 0) {
      return [];
    }
    return cities.filter((city) =>
      city.city_name.toLowerCase().startsWith(citySearch.toLowerCase())
    );
  }, [cities, citySearch]);

  const filteredDistricts = useMemo(() => {
    if (!selectedState) {
      return [];
    }
    const selectedStateName = selectedState.state_name
      .toLowerCase()
      .replace(/\./g, "")
      .trim();
    const districts = Object.keys(
      optimizedIndianData[selectedStateName]?.districts || {}
    );
    return districts.filter((district) =>
      district.toLowerCase().startsWith(districtSearch.toLowerCase())
    );
  }, [optimizedIndianData, selectedState, districtSearch]);

  const filteredTaluks = useMemo(() => {
    if (!selectedState || !districtSearch) return [];
    const selectedStateName = selectedState.state_name
      .toLowerCase()
      .replace(/\./g, "")
      .trim();
    const taluks = Object.keys(
      optimizedIndianData[selectedStateName]?.districts[districtSearch]
        ?.taluks || {}
    );
    return taluks.filter((taluk) =>
      taluk.toLowerCase().startsWith(talukSearch.toLowerCase())
    );
  }, [optimizedIndianData, selectedState, districtSearch, talukSearch]);

  {
    /* Searching in Branch Offices */
  }
  const filteredBranchOffices = useMemo(() => {
    if (!selectedState || !districtSearch || !talukSearch) return [];
    const selectedStateName = selectedState.state_name
      .toLowerCase()
      .replace(/\./g, "")
      .trim();
    const offices =
      optimizedIndianData[selectedStateName]?.districts[districtSearch]
        ?.offices || [];
    return offices
      .filter(
        (office) =>
          office.taluk.toLowerCase() === talukSearch.toLowerCase() &&
          office.officeName
            .toLowerCase()
            .startsWith(branchOfficeSearch.toLowerCase())
      )
      .map(({ officeName, pincode, id }) => ({ officeName, pincode, id }));
  }, [
    optimizedIndianData,
    selectedState,
    districtSearch,
    talukSearch,
    branchOfficeSearch,
  ]);
  const validateAllFields = useCallback(() => {
    const newErrors = {};
    const firstNameErrors = [];
    const lastNameErrors = [];

    // Validate firstName
    if (!firstName || !firstName.trim()) {
      firstNameErrors.push("First name is required");
    } else {
      if (firstName.trim().length < 3) {
        firstNameErrors.push("First Name should be at least 3 characters long");
      }
      if (!/^[A-Za-z]+$/.test(firstName.trim())) {
        firstNameErrors.push("First name should contain only alphabets");
      }
      if (firstName.trim().length > 10) {
        firstNameErrors.push("First name should not exceed 10 characters");
      }
      const sanitizedFName = DOMPurify.sanitize(firstName.trim());
      if (sanitizedFName !== firstName.trim()) {
        firstNameErrors.push("First Name contains invalid characters");
      }
    }

    if (firstNameErrors.length > 0) {
      newErrors.firstName = firstNameErrors;
    }

    // Validate lastName
    if (!lastName || !lastName.trim()) {
      lastNameErrors.push("Last name is required");
    } else {
      if (lastName.trim().length < 2) {
        lastNameErrors.push("Last name should be at least 2 characters long");
      }
      if (!/^[A-Za-z]+$/.test(lastName.trim())) {
        lastNameErrors.push("Last name should contain only alphabets");
      }
      if (lastName.trim().length > 10) {
        lastNameErrors.push("Last name should not exceed 10 characters");
      }
      const sanitizedLName = DOMPurify.sanitize(lastName.trim());
      if (sanitizedLName !== lastName.trim()) {
        lastNameErrors.push("Last name contains invalid characters");
      }
    }

    if (lastNameErrors.length > 0) {
      newErrors.lastName = lastNameErrors;
    }

    // Validate email
    if (!email || !email.trim()) {
      newErrors.email = "Email is required";
    } else if (!/\S+@\S+\.\S+/.test(email)) {
      newErrors.email = "Email is invalid";
    }

    // Validate phoneNumber
    if (!phoneNumber || !phoneNumber.trim()) {
      newErrors.phoneNumber = "Phone number is required";
    } else if (!/^\d{10}$/.test(phoneNumber.replace(/\D/g, ""))) {
      newErrors.phoneNumber = "Phone number must be 10 digits";
    }

    // Validate country
    if (!selectedCountry) {
      newErrors.country = "Country is required";
    }

    // Validate state
    if (!selectedState) {
      newErrors.state = "State is required";
    }

    // Validate Indian-specific fields
    if (isIndianPlaces) {
      if (!selectedDistrict || !selectedDistrict.trim()) {
        newErrors.district = "District is required";
      }
      if (!selectedTaluk || !selectedTaluk.trim()) {
        newErrors.taluk = "Taluk is required";
      }
      if (!selectedBranchOffice || !selectedBranchOffice.trim()) {
        newErrors.branchOffice = "Branch office is required";
      }
      if (!selectedPincode || !selectedPincode.trim()) {
        newErrors.pincode = "Pincode is required";
      }
    } else {
      // Validate city for non-Indian places
      if (!selectedCity || !selectedCity.trim()) {
        newErrors.city = "City is required";
      }
    }

    // Validate addressLine
    if (!addressLine || !addressLine.trim()) {
      newErrors.addressLine = "Address is required";
    } else {
      const sanitizedAddress = DOMPurify.sanitize(addressLine.trim());
      if (sanitizedAddress !== addressLine.trim()) {
        newErrors.addressLine = "Address contains invalid characters";
      }
    }

    // Validate landmark
    if (!landmark || !landmark.trim()) {
      newErrors.landmark = "Landmark is required";
    } else {
      const sanitizedLandmark = DOMPurify.sanitize(landmark.trim());
      if (sanitizedLandmark !== landmark.trim()) {
        newErrors.landmark = "Landmark contains invalid characters";
      }
    }

    setErrors(newErrors);
    return newErrors;
    }, [
    firstName,
    lastName,
    email,
    phoneNumber,
    selectedCountry,
    selectedState,
    selectedCity,
    selectedDistrict,
    selectedTaluk,
    selectedBranchOffice,
    selectedPincode,
    addressLine,
    landmark,
    isIndianPlaces,
  ]);
  const validateForm = useCallback(
    (field, value) => {
      const newErrors = { ...errors };
      // Validate firstName
      if (field === "firstName") {
        newErrors.firstName = [];
        if (!value || value.trim().length === 0) {
          newErrors.firstName.push("First name is required");
        }
        if (value.trim().length < 3) {
          newErrors.firstName.push(
            "First name should be at least 3 characters long"
          );
        }
        if (!/^[A-Za-z]+$/.test(value.trim())) {
          newErrors.firstName.push("First name should contain only alphabets");
        }
        if (value.trim().length > 10) {
          newErrors.firstName.push(
            "First name should not exceed 10 characters"
          );
        }
        if (newErrors.firstName.length === 0) {
          delete newErrors.firstName;
        }
      }

      // Validate lastName
      if (field === "lastName") {
        newErrors.lastName = [];
        if (!value || value.trim().length === 0) {
          newErrors.lastName.push("Last name is required");
        }
        if (value.trim().length < 2) {
          newErrors.lastName.push(
            "Last name should be at least 2 characters long"
          );
        }
        if (!/^[A-Za-z]+$/.test(value.trim())) {
          newErrors.lastName.push("Last name should contain only alphabets");
        }
        if (value.trim().length > 10) {
          newErrors.lastName.push("Last name should not exceed 10 characters");
        } else {
          if (newErrors.lastName.length === 0) {
            delete newErrors.lastName;
          }
        }
      }

      // Validate email
      if (field === "email") {
        newErrors.email = [];
        if (!value || !value.trim()) {
          newErrors.email.push("Email is required");
        } else if (!/\S+@\S+\.\S+/.test(value)) {
          newErrors.email.push("Email is invalid");
        } else {
          if (newErrors.email.length === 0) {
            delete newErrors.email;
          }
        }
      }

      // Validate phoneNumber
      if (field === "phoneNumber") {
        newErrors.phoneNumber = [];
        if (!value || !value.trim()) {
          newErrors.phoneNumber = "Phone number is required";
        } else if (!/^\d{10}$/.test(value.replace(/\D/g, ""))) {
          newErrors.phoneNumber = "Phone number must be 10 digits";
        } else {
          if (newErrors.phoneNumber.length === 0) {
            delete newErrors.phoneNumber;
          }
        }
      }

      // Validate country
      if (field === "country") {
        if (!value) {
          newErrors.country = "Country is required";
        } else {
          delete newErrors.country;
        }
      }

      // Validate state
      if (field === "state") {
        if (!value) {
          newErrors.state = "State is required";
        } else {
          delete newErrors.state;
        }
      }

      // Validate Indian-specific fields
      if (isIndianPlaces) {
        if (field === "district") {
          if (!value || !value.trim()) {
            newErrors.district = "District is required";
          } else {
            delete newErrors.district;
          }
        }
        if (field === "taluk") {
          if (!value || !value.trim()) {
            newErrors.taluk = "Taluk is required";
          } else {
            delete newErrors.taluk;
          }
        }
        if (field === "branchOffice") {
          if (!value || !value.trim()) {
            newErrors.branchOffice = "Branch office is required";
          } else {
            delete newErrors.branchOffice;
          }
        }
        if (field === "pincode") {
          if (!value || !value.trim()) {
            newErrors.pincode = "Pincode is required";
          } else {
            delete newErrors.pincode;
          }
        }
      } else {
        // Validate city for non-Indian places
        if (field === "city") {
          if (!value || !value.trim()) {
            newErrors.city = "City is required";
          } else {
            delete newErrors.city;
          }
        }
      }

      // Validate addressLine
      if (field === "addressLine") {
        newErrors.addressLine = [];
        if (!value || !value.trim()) {
          newErrors.addressLine = "Address is required";
        } else {
          const sanitizedAddress = DOMPurify.sanitize(value.trim());
          if (sanitizedAddress !== value.trim()) {
            newErrors.addressLine = "Address contains invalid characters";
          } else {
            if (newErrors.addressLine.length === 0) {
              delete newErrors.addressLine;
            }
          }
        }
      }

      // Validate landmark
      if (field === "landmark") {
        newErrors.landmark = [];
        if (!value || !value.trim()) {
          newErrors.landmark = "Landmark is required";
        } else {
          const sanitizedLandmark = DOMPurify.sanitize(value.trim());
          if (sanitizedLandmark !== value.trim()) {
            newErrors.landmark = "Landmark contains invalid characters";
          } else {
            if (newErrors.landmark.length === 0) {
              delete newErrors.landmark;
            }
          }
        }
      }

      console.log("Validation errors:", newErrors);
      setErrors(newErrors);
      return Object.keys(newErrors).length === 0 ? null : newErrors;
    },
    [isIndianPlaces, errors]
  );

  const resetForm = useCallback(() => {
    setFirstName("");
    setLastName("");
    setEmail("");
    setPhoneNumber("");
    setAddressLine("");
    setLandmark("");
    setSelectedCountry(null);
    setSelectedState(null);
    setSelectedCity(null);
    setSelectedDistrict(null);
    setSelectedTaluk(null);
    setSelectedBranchOffice(null);
    setSelectedPincode(null);
    setCountrySearch("");
    setStateSearch("");
    setCitySearch("");
    setDistrictSearch("");
    setTalukSearch("");
    setBranchOfficeSearch("");
    setPincodeSearch("");
    setErrors({});
  }, []);
  const handleFirstNameChange = useCallback(
    (e) => {
      const value = e.target.value;
      setFirstName(value);
      const newErrors = validateForm("firstName", value);
      setErrors((prevErrors) => ({ ...prevErrors, ...newErrors }));
    },
    [validateForm]
  );

  const handleLastNameChange = useCallback(
    (e) => {
      const value = e.target.value;
      setLastName(value);
      const newErrors = validateForm("lastName", value);
      setErrors((prevErrors) => ({ ...prevErrors, ...newErrors }));
    },
    [validateForm]
  );

  const handleMailChange = useCallback(
    (e) => {
      const value = e.target.value;
      setEmail(value);
      const newErrors = validateForm("email", value);
      setErrors((prevErrors) => ({ ...prevErrors, ...newErrors }));
    },
    [validateForm]
  );

  const handlePhoneNumberChange = useCallback(
    (e) => {
      const value = e.target.value;
      setPhoneNumber(value);
      const newErrors = validateForm("phoneNumber", value);
      setErrors((prevErrors) => ({ ...prevErrors, ...newErrors }));
    },
    [validateForm]
  );

  const handleAddressLineChange = useCallback(
    (e) => {
      const value = e.target.value;
      setAddressLine(value);
      const newErrors = validateForm("addressLine", value);
      setErrors((prevErrors) => ({ ...prevErrors, ...newErrors }));
    },
    [validateForm]
  );

  const handleLandmarkChange = useCallback(
    (e) => {
      const value = e.target.value;
      setLandmark(value);
      const newErrors = validateForm("landmark", value);
      setErrors((prevErrors) => ({ ...prevErrors, ...newErrors }));
    },
    [validateForm]
  );

  const handleCountrySelect = useCallback(
    (country) => {
      setSelectedCountry(country);
      setCountrySearch(country.name);
      setSelectedState(null);
      setStateSearch("");
      setCitySearch("");
      setCities([]);
      setIsSearchingCountry(false);
      setIsIndianPlaces(country.name === "India");
      validateForm("country", country.name);
      if (country.name !== "India") {
        setDistrictSearch("");
        setTalukSearch("");
        setBranchOfficeSearch("");
        setPincodeSearch("");
        const newErrors = validateForm("country", country.name);
        setErrors((prevErrors) => ({ ...prevErrors, ...newErrors }));
      }
    },
    [validateForm]
  );

  const handleStateSelect = useCallback(
    (state) => {
      setSelectedState(state);
      setStateSearch(state.state_name);
      setCitySearch("");
      setIsSearchingState(false);
      const newErrors = validateForm("state", state.state_name);
      setErrors((prevErrors) => ({ ...prevErrors, ...newErrors }));

      if (isIndianPlaces) {
        setCitySearch("");
        setDistrictSearch("");
        setTalukSearch("");
        setBranchOfficeSearch("");
        setPincodeSearch("");
        const newErrors = validateForm("state", state.state_name);
        setErrors((prevErrors) => ({ ...prevErrors, ...newErrors }));
      } else {
        // Reset Indian-specific fields
        setDistrictSearch("");
        setTalukSearch("");
        setBranchOfficeSearch("");
        setPincodeSearch("");
      }
    },
    [isIndianPlaces, validateForm]
  );

  const handleCitySelect = useCallback(
    (city) => {
      setSelectedCity(city.city_name);
      setCitySearch(city.city_name);
      setIsSearchingCity(false);
      const newErrors = validateForm("city", city.city_name);
      setErrors((prevErrors) => ({ ...prevErrors, ...newErrors }));
    },
    [validateForm]
  );

  const handleDistrictSelect = useCallback(
    (district) => {
      setDistrictSearch(district);
      setSelectedDistrict(district);
      setIsSearchingDistrict(false);
      setTalukSearch("");
      setBranchOfficeSearch("");
      setPincodeSearch("");
      const newErrors = validateForm("district", district);
      setErrors((prevErrors) => ({ ...prevErrors, ...newErrors }));
    },
    [validateForm]
  );

  const handleTalukSelect = useCallback(
    (taluk) => {
      setSelectedTaluk(taluk);
      setIsSearchingTaluk(false);
      setTalukSearch(taluk);
      setBranchOfficeSearch("");
      setPincodeSearch("");
      const newErrors = validateForm("taluk", taluk);
      setErrors((prevErrors) => ({ ...prevErrors, ...newErrors }));
    },
    [validateForm]
  );

  const handleBranchOfficeSelect = useCallback(
    (office) => {
      setSelectedBranchOffice(office.officeName);
      setBranchOfficeSearch(office.officeName);
      setPincodeSearch(office.pincode.toString());
      setSelectedPincode(office.pincode.toString());
      setIsSearchingOffice(false);
      const newErrors = validateForm("branchOffice", office.officeName);
      setErrors((prevErrors) => ({ ...prevErrors, ...newErrors }));
    },
    [validateForm]
  );

  const handleSubmit = useCallback(
    async (e) => {
      if (e) {
        e.preventDefault();
      }
      if (isSubmitting) {
        setShowResubmitModal(true);
        return;
      }

      setErrors({});
      setIsSubmitting(true);

      const formErrors = validateAllFields();
      console.log("form errors:", formErrors);
      
      if (Object.keys(formErrors).length === 0) {
        try {
          const formData = {
            timeOfSubmission: new Date().toISOString(),
            personalInfo: { firstName, lastName, email, phoneNumber },
            address: {
              addressLine,
              landmark,
              country: selectedCountry?.name,
              state: selectedState?.state_name,
              city: isIndianPlaces ? selectedBranchOffice : selectedCity,
              district: isIndianPlaces ? selectedDistrict : null,
              taluk: isIndianPlaces ? selectedTaluk : null,
              pincode: isIndianPlaces ? selectedPincode : null,
            },
          };

          // Replace this with your actual API call
          await new Promise((resolve) => setTimeout(resolve, 2000));

          setFormData(formData);
          setSubmissionStatus({
            type: "success",
            message: "Form submitted successfully!",
          });
          resetForm();
        } catch (error) {
          console.error("Submission error:", error);
          setSubmissionStatus({
            type: "error",
            message: "Form submission failed. Please try again.",
          });
        }
      } else {
        setErrors(formErrors);
        setSubmissionStatus({
          type: "error",
          message: "Please correct the errors in the form.",
        });
      }

      setIsSubmitting(false);
    },
    [
      isSubmitting,
      resetForm,
      firstName,
      lastName,
      email,
      phoneNumber,
      addressLine,
      landmark,
      selectedCountry,
      selectedState,
      selectedCity,
      selectedDistrict,
      selectedTaluk,
      selectedBranchOffice,
      selectedPincode,
      isIndianPlaces,
      validateAllFields,
    ]
  );

  function Loading() {
    return <h2>🌀 Loading...</h2>;
  }
  return (
    <div className="flex flex-col md:flex-row gap-8 p-6 justify-center align-middle bg-stone-50">
      <div className="flex h-auto w-full lg:w-2/4 bg-white drop-shadow-md rounded-[25px] p-8 transition-all ease-in-out duration-200">
        <form onSubmit={handleSubmit} className="flex flex-col w-full">
          <div className="flex flex-col gap-y-6">
            <div className="flex gap-5">
              {/* First Name */}
              <div className="flex-1 flex flex-col relative">
                <label
                  htmlFor="first-name"
                  className="text-base font-bold text-gray-700 mb-1 h-10 flex items-start"
                >
                  First Name
                </label>
                <input
                  id="first-name"
                  name="firstName"
                  value={firstName}
                  onChange={(e) => {
                    handleFirstNameChange(e);
                  }}
                  type="text"
                  placeholder="Rajkiran"
                  className={`w-full px-3 py-2 border ${
                    errors.firstName ? "border-red-500" : "border-gray-300"
                  } rounded-md focus:outline-none focus:ring-2 ${
                    errors.firstName
                      ? "focus:ring-red-500"
                      : "focus:ring-blue-500"
                  } disabled:bg-gray-100 disabled:cursor-not-allowed`}
                />
                {errors.firstName && Array.isArray(errors.firstName) && (
                  <div className="text-red-500 flex flex-col text-xs mt-1 gap-1">
                    {errors.firstName.map((error, index) => (
                      <p key={index}>{error}</p>
                    ))}
                  </div>
                )}
              </div>
              {/* Last Name */}
              <div className="flex-1 flex flex-col relative">
                <label
                  htmlFor="last-name"
                  className="text-base font-bold text-gray-700 mb-1 h-10 flex items-start"
                >
                  Last Name
                </label>
                <input
                  id="last-name"
                  name="lastName"
                  value={lastName}
                  onChange={(e) => handleLastNameChange(e)}
                  type="text"
                  placeholder="Dev"
                  className={`w-full px-3 py-2 border ${
                    errors.lastName ? "border-red-500" : "border-gray-300"
                  } rounded-md focus:outline-none focus:ring-2 ${
                    errors.lastName
                      ? "focus:ring-red-500"
                      : "focus:ring-blue-500"
                  } disabled:bg-gray-100 disabled:cursor-not-allowed`}
                />
                {errors.lastName && Array.isArray(errors.lastName) && (
                  <div className="text-red-500 flex flex-col text-xs mt-1 gap-1">
                    {errors.lastName.map((error, index) => (
                      <p key={index}>{error}</p>
                    ))}
                  </div>
                )}
              </div>
            </div>

            <div className="flex gap-5">
              {/* Email */}
              <div className="flex-1 flex flex-col relative">
                <label
                  htmlFor="email"
                  className="text-base font-bold text-gray-700 mb-1 h-10 flex items-start"
                >
                  Email
                </label>
                <input
                  id="email"
                  name="email"
                  value={email}
                  onChange={(e) => handleMailChange(e)}
                  type="email"
                  placeholder="rajkiran.dev@example.com"
                  className={`w-full px-3 py-2 border ${
                    errors.email ? "border-red-500" : "border-gray-300"
                  } rounded-md focus:outline-none focus:ring-2 ${
                    errors.email ? "focus:ring-red-500" : "focus:ring-blue-500"
                  } disabled:bg-gray-100 disabled:cursor-not-allowed`}
                />
                {errors.email && (
                  <p className="text-red-500 text-xs mt-1">{errors.email}</p>
                )}
              </div>
              {/* Phone Number */}
              <div className="flex-1 flex flex-col relative">
                <label
                  htmlFor="phone-number"
                  className="text-base font-bold text-gray-700 mb-1 h-10 flex items-start"
                >
                  Phone No
                </label>
                <input
                  id="phone-number"
                  name="phoneNumber"
                  value={phoneNumber}
                  onChange={(e) => handlePhoneNumberChange(e)}
                  type="tel"
                  placeholder="123-456-7890"
                  className={`w-full px-3 py-2 border ${
                    errors.phoneNumber ? "border-red-500" : "border-gray-300"
                  } rounded-md focus:outline-none focus:ring-2 ${
                    errors.phoneNumber
                      ? "focus:ring-red-500"
                      : "focus:ring-blue-500"
                  } disabled:bg-gray-100 disabled:cursor-not-allowed`}
                />
                {errors.phoneNumber && (
                  <p className="text-red-500 text-xs mt-1">
                    {errors.phoneNumber}
                  </p>
                )}
              </div>
            </div>
            <div className="flex gap-5">
              {/* Country Input */}
              <div
                className="flex-1 flex flex-col relative"
                ref={countryDropdownRef}
              >
                <label
                  htmlFor="country-search"
                  className="text-base font-bold text-gray-700 mb-1 h-10 flex items-start"
                >
                  Country
                </label>
                <input
                  id="country-search"
                  name="countrySearch"
                  value={countrySearch}
                  onChange={(e) => {
                    setCountrySearch(e.target.value);
                    setIsSearchingCountry(true);
                    if (!e.target.value) {
                      setSelectedCountry(null);
                      setStates([]);
                      setCities([]);
                    }
                  }}
                  onFocus={() => setIsSearchingCountry(true)}
                  type="text"
                  placeholder="choose country"
                  className={`w-full px-3 py-2 border ${
                    errors.country ? "border-red-500" : "border-gray-300"
                  } rounded-md focus:outline-none focus:ring-2 ${
                    errors.country
                      ? "focus:ring-red-500"
                      : "focus:ring-blue-500"
                  } disabled:bg-gray-100 disabled:cursor-not-allowed`}
                />
                {errors.country && (
                  <p className="text-red-500 text-xs mt-1">{errors.country}</p>
                )}
                {isSearchingCountry && (
                  <Suspense fallback={<Loading />}>
                    {isLoadingCountries ? (
                      <Loading />
                    ) : (
                      <ul className="absolute z-10 w-full top-full left-0 mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
                        {filteredCountries.map((country) => (
                          <li
                            key={country.id}
                            onClick={() => {
                              handleCountrySelect(country);
                              setIsSearchingCountry(false);
                            }}
                            className="px-3 py-2 cursor-pointer hover:bg-gray-100"
                          >
                            {country.name}
                          </li>
                        ))}
                      </ul>
                    )}
                  </Suspense>
                )}
              </div>

              {/* State Input */}
              <div
                className="flex-1 flex flex-col relative"
                ref={stateDropdownRef}
              >
                <label
                  htmlFor="state-search"
                  className="text-base font-bold text-gray-700 mb-1 h-10 flex items-start"
                >
                  State
                </label>
                <input
                  id="state-search"
                  type="text"
                  name="states"
                  value={stateSearch}
                  onChange={(e) => {
                    setStateSearch(e.target.value);
                    setIsSearchingState(true);
                    if (!e.target.value) {
                      setSelectedState(null);
                      setCities([]);
                      setDistrictSearch("");
                      setTalukSearch("");
                      setBranchOfficeSearch("");
                      setPincodeSearch("");
                    }
                  }}
                  onFocus={() => setIsSearchingState(true)}
                  placeholder="Choose state"
                  disabled={!selectedCountry}
                  className={`w-full px-3 py-2 border ${
                    errors.state ? "border-red-500" : "border-gray-300"
                  } rounded-md focus:outline-none focus:ring-2 ${
                    errors.state ? "focus:ring-red-500" : "focus:ring-blue-500"
                  } disabled:bg-gray-100 disabled:cursor-not-allowed`}
                />
                {errors.state && (
                  <p className="text-red-500 text-xs mt-1">{errors.state}</p>
                )}
                {selectedCountry && isSearchingState && (
                  <ul className="absolute z-10 w-full top-full left-0 mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
                    {filteredStates.map((state) => (
                      <li
                        key={state.state_id}
                        onClick={() => {
                          handleStateSelect(state);
                          setIsSearchingState(false);
                        }}
                        className="px-3 py-2 cursor-pointer hover:bg-gray-100"
                      >
                        {state.state_name.charAt(0).toLowerCase() +
                          state.state_name.slice(1).toLowerCase()}
                      </li>
                    ))}
                  </ul>
                )}
              </div>
            </div>

            {isIndianPlaces ? (
              <>
                <div className="flex gap-5">
                  {/* District Input */}
                  <div
                    className="flex-1 flex flex-col relative"
                    ref={districtDropdownRef}
                  >
                    <label
                      htmlFor="district-search"
                      className="text-base font-bold text-gray-700 mb-1 h-10 flex items-start"
                    >
                      District
                    </label>
                    <input
                      id="district-search"
                      type="text"
                      name="district"
                      value={districtSearch}
                      onChange={(e) => {
                        setDistrictSearch(e.target.value);
                        setIsSearchingDistrict(true);
                        if (!e.target.value) {
                          setDistrictSearch("");
                          setTalukSearch("");
                          setBranchOfficeSearch("");
                          setPincodeSearch("");
                        }
                      }}
                      onFocus={() => setIsSearchingDistrict(true)}
                      placeholder="Choose district"
                      disabled={!selectedState}
                      className={`w-full px-3 py-2 border ${
                        errors.district ? "border-red-500" : "border-gray-300"
                      } rounded-md focus:outline-none focus:ring-2 ${
                        errors.district
                          ? "focus:ring-red-500"
                          : "focus:ring-blue-500"
                      } disabled:bg-gray-100 disabled:cursor-not-allowed`}
                    />
                    {errors.district && (
                      <p className="text-red-500 text-xs mt-1">
                        {errors.district}
                      </p>
                    )}
                    {selectedState && isSearchingDistrict && (
                      <ul className="absolute z-10 w-full top-full left-0 mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
                        {filteredDistricts.map((district) => (
                          <li
                            key={district}
                            onClick={() => {
                              handleDistrictSelect(district);
                              setIsSearchingDistrict(false);
                            }}
                            className="px-3 py-2 cursor-pointer hover:bg-gray-100"
                          >
                            {district}
                          </li>
                        ))}
                      </ul>
                    )}
                  </div>

                  {/* Taluk Input */}
                  <div
                    className="flex-1 flex flex-col relative"
                    ref={talukDropdownRef}
                  >
                    <label
                      htmlFor="taluk-search"
                      className="text-base font-bold text-gray-700 mb-1 h-10 flex items-start"
                    >
                      Taluk
                    </label>
                    <input
                      id="taluk-search"
                      type="text"
                      name="taluk"
                      value={talukSearch}
                      onChange={(e) => {
                        setTalukSearch(e.target.value);
                        setIsSearchingTaluk(true);
                        if (!e.target.value) {
                          setTalukSearch("");
                          setBranchOfficeSearch("");
                          setPincodeSearch("");
                        }
                      }}
                      onFocus={() => setIsSearchingTaluk(true)}
                      placeholder="Choose taluk"
                      disabled={!districtSearch}
                      className={`w-full px-3 py-2 border ${
                        errors.taluk ? "border-red-500" : "border-gray-300"
                      } rounded-md focus:outline-none focus:ring-2 ${
                        errors.taluk
                          ? "focus:ring-red-500"
                          : "focus:ring-blue-500"
                      } disabled:bg-gray-100 disabled:cursor-not-allowed`}
                    />
                    {errors.taluk && (
                      <p className="text-red-500 text-xs mt-1">
                        {errors.taluk}
                      </p>
                    )}
                    {districtSearch && isSearchingTaluk && (
                      <ul className="absolute z-10 w-full top-full left-0 mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
                        {filteredTaluks.map((taluk) => (
                          <li
                            key={taluk}
                            onClick={() => {
                              handleTalukSelect(taluk);
                              setIsSearchingTaluk(false);
                            }}
                            className="px-3 py-2 cursor-pointer hover:bg-gray-100"
                          >
                            {taluk}
                          </li>
                        ))}
                      </ul>
                    )}
                  </div>
                </div>

                <div className="flex gap-5">
                  {/* Branch Office Input */}
                  <div
                    className="flex-1 flex flex-col relative"
                    ref={branchOfficeDropdownRef}
                  >
                    <label
                      htmlFor="branch-office-search"
                      className="text-base font-bold text-gray-700 mb-1 h-10 flex items-start"
                    >
                      Branch Office
                    </label>
                    <div className="flex-1 relative">
                      <input
                        id="branch-office-search"
                        type="text"
                        name="branchOffice"
                        value={branchOfficeSearch}
                        onChange={(e) => {
                          setBranchOfficeSearch(e.target.value);
                          setIsSearchingOffice(true);
                          if (!e.target.value) {
                            setBranchOfficeSearch("");
                            setPincodeSearch("");
                          }
                        }}
                        onFocus={() => setIsSearchingOffice(true)}
                        placeholder="Choose branch office"
                        disabled={!talukSearch}
                        className={`w-full px-3 py-2 border ${
                          errors.branchOffice
                            ? "border-red-500"
                            : "border-gray-300"
                        } rounded-md focus:outline-none focus:ring-2 ${
                          errors.branchOffice
                            ? "focus:ring-red-500"
                            : "focus:ring-blue-500"
                        } disabled:bg-gray-100 disabled:cursor-not-allowed`}
                      />
                      {errors.branchOffice && (
                        <p className="text-red-500 text-xs mt-1">
                          {errors.branchOffice}
                        </p>
                      )}
                      {talukSearch && isSearchingOffice && (
                        <ul className="absolute z-10 w-full top-full left-0 mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
                          {filteredBranchOffices.map((office) => (
                            <li
                              key={office.id}
                              onClick={() => {
                                handleBranchOfficeSelect(office);
                                setIsSearchingOffice(false);
                              }}
                              className="px-3 py-2 cursor-pointer hover:bg-gray-100"
                            >
                              {office.officeName} - {office.pincode}
                            </li>
                          ))}
                        </ul>
                      )}
                    </div>
                  </div>

                  {/* Pincode Input */}
                  <div className="flex-1 flex flex-col relative">
                    <label
                      htmlFor="pincode"
                      className="text-base font-bold text-gray-700 mb-1 h-10 flex items-start"
                    >
                      Pincode
                    </label>
                    <div className="flex-1">
                      <input
                        id="pincode"
                        type="text"
                        name="pincode"
                        value={pincodeSearch}
                        onChange={(e) => {
                          setPincodeSearch(e.target.value);
                          if (!e.target.value) {
                            setPincodeSearch("");
                          }
                        }}
                        placeholder="Pincode"
                        readOnly
                        className={`w-full px-3 py-2 border ${
                          errors.pincode ? "border-red-500" : "border-gray-300"
                        } rounded-md focus:outline-none focus:ring-2 ${
                          errors.pincode
                            ? "focus:ring-red-500"
                            : "focus:ring-blue-500"
                        } disabled:bg-gray-100 disabled:cursor-not-allowed`}
                      />
                      {errors.pincode && (
                        <p className="text-red-500 text-xs mt-1">
                          {errors.pincode}
                        </p>
                      )}
                    </div>
                  </div>
                </div>
              </>
            ) : (
              // City Input for non-Indian places
              <div className="relative" ref={cityDropdownRef}>
                <label
                  htmlFor="city-search"
                  className="block text-sm font-medium text-gray-700 mb-1"
                >
                  City
                </label>
                <input
                  id="city-search"
                  type="text"
                  name="city"
                  value={citySearch}
                  onChange={(e) => {
                    setCitySearch(e.target.value);
                    setIsSearchingCity(true);
                  }}
                  onFocus={() => setIsSearchingCity(true)}
                  placeholder="Choose city"
                  disabled={!selectedState}
                  className={`w-full px-3 py-2 border ${
                    errors.city ? "border-red-500" : "border-gray-300"
                  } rounded-md focus:outline-none focus:ring-2 ${
                    errors.city ? "focus:ring-red-500" : "focus:ring-blue-500"
                  } disabled:bg-gray-100 disabled:cursor-not-allowed`}
                />
                {errors.city && (
                  <p className="text-red-500 text-xs mt-1">{errors.city}</p>
                )}
                {selectedState && isSearchingCity && !isLoadingCities && (
                  <ul className="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
                    {filteredCities.length > 0 ? (
                      filteredCities.map((city) => (
                        <li
                          key={city.city_id}
                          onClick={() => {
                            handleCitySelect(city);
                            setIsSearchingCity(false);
                          }}
                          className="px-3 py-2 cursor-pointer hover:bg-gray-100"
                        >
                          {city.city_name}
                        </li>
                      ))
                    ) : (
                      <li className="px-3 py-2 text-gray-500">
                        No cities found
                      </li>
                    )}
                  </ul>
                )}
              </div>
            )}
            {/* Flat/Floor/H.No */}
            <div className="flex gap-5">
              <div className="flex-1 flex flex-col relative">
                <label
                  htmlFor="flat-floor-hno"
                  className="text-base font-bold text-gray-700 mb-1 h-10 flex items-start"
                >
                  Address
                </label>
                <input
                  id="flat-floor-hno"
                  type="text"
                  name="flatFloorHno"
                  value={addressLine}
                  onChange={(e) => handleAddressLineChange(e)}
                  placeholder="Flat/Floor/H.No"
                  className={`w-full px-3 py-2 border ${
                    errors.addressLine ? "border-red-500" : "border-gray-300"
                  } rounded-md focus:outline-none focus:ring-2 ${
                    errors.addressLine
                      ? "focus:ring-red-500"
                      : "focus:ring-blue-500"
                  } disabled:bg-gray-100 disabled:cursor-not-allowed`}
                />
                {errors.addressLine && (
                  <p className="text-red-500 text-xs mt-1">
                    {errors.addressLine}
                  </p>
                )}
              </div>
              {/* Landmark */}
              <div className="flex-1 flex flex-col relative">
                <label
                  htmlFor="landmark"
                  className="text-base font-bold text-gray-700 mb-1 h-10 flex items-start"
                >
                  Landmark
                </label>
                <input
                  id="landmark"
                  type="text"
                  name="landmark"
                  value={landmark}
                  onChange={(e) => handleLandmarkChange(e)}
                  placeholder="Landmark"
                  className={`w-full px-3 py-2 border ${
                    errors.landmark ? "border-red-500" : "border-gray-300"
                  } rounded-md focus:outline-none focus:ring-2 ${
                    errors.landmark
                      ? "focus:ring-red-500"
                      : "focus:ring-blue-500"
                  } disabled:bg-gray-100 disabled:cursor-not-allowed`}
                />
                {errors.landmark && (
                  <p className="text-red-500 text-xs mt-1">{errors.landmark}</p>
                )}
              </div>
            </div>
            <button
              className="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50"
              type="submit"
              disabled={isSubmitting}
            >
              {isSubmitting ? "Submitting..." : "Submit"}
            </button>
            {submissionStatus && (
              <div
                className={`mt-4 p-2 rounded-md ${
                  submissionStatus.type === "success"
                    ? "bg-green-100 text-green-800"
                    : "bg-red-100 text-red-800"
                }`}
              >
                {submissionStatus.message}
              </div>
            )}
          </div>
        </form>
        {showResubmitModal && (
          <ResubmitModal
            setShowResubmitModal={setShowResubmitModal}
            handleSubmit={handleSubmit}
          />
        )}
      </div>
      {/* Form Data Table */}
      {Object.keys(formData).length > 0 && (
        <div className="w-auto md:w-1.5/4 transition-all ease-in-out duration-150">
          <h2 className="text-xl text-center bg-lime-200 font-bold text-gray-700 mb-4 md:p-4 ">
            Form Data
          </h2>
          <div className="overflow-x-auto">
            <table className="w-full border-collapse">
              <tbody>
                {formData.timeOfSubmission && (
                  <tr>
                    <td className="px-6 py-4 text-left text-sm text-gray-900">
                      Time of Submission
                    </td>
                    <td className="px-6 py-4 text-left text-sm text-gray-900">
                      {new Date(formData.timeOfSubmission).toLocaleString()}
                    </td>
                  </tr>
                )}
                {Object.entries(formData).map(
                  ([sectionTitle, sectionData], sectionIndex) => {
                    if (sectionTitle === "timeOfSubmission") return null;
                    if (typeof sectionData !== "object" || sectionData === null)
                      return null;

                    return (
                      <React.Fragment key={sectionIndex}>
                        <tr>
                          <td
                            colSpan="2"
                            className="px-6 py-4 text-left text-sm font-bold text-gray-900 bg-violet-100"
                          >
                            {sectionTitle}
                          </td>
                        </tr>
                        {Object.entries(sectionData).map(
                          ([fieldName, fieldValue], index) => (
                            <tr key={index}>
                              <td className="px-6 py-4 text-left text-stone-800 bg-yellow-100">
                                {fieldName.charAt(0).toUpperCase() +
                                  fieldName.slice(1).toLowerCase()}
                              </td>
                              <td className="px-6 py-4 text-left text-sm text-gray-600 border">
                                {fieldValue !== null && fieldValue !== undefined
                                  ? String(fieldValue)
                                  : "N/A"}
                              </td>
                            </tr>
                          )
                        )}
                      </React.Fragment>
                    );
                  }
                )}
              </tbody>
            </table>
          </div>
        </div>
      )}
      {/* End of */}
    </div>
  );
}

export default IndianPincodesAdv;
