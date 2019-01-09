import * as axios from "axios";

export const axiosSetJwt = (jwtToken) => {
    axios['defaults'].headers.common['Authorization'] = `Bearer ${jwtToken}`;
};

export const axiosUnsetJwt = () => {
    axios['defaults'].headers.common['Authorization'] = null;
};
