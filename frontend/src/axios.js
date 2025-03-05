import axios from 'axios';

// axios.defaults.withCredentials = true;
axios.defaults.baseURL="http://18.134.22.0:8000/api/"
axios.defaults.headers.common['Authorization'] = 'Bearer '+ localStorage.getItem('token');
