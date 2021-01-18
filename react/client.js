import axios from 'axios';
import store from '../store';
import { refresh } from '../actions'

const client = {
    fetch: async (query, variables) => {
        const response = await axios.post(process.env.REACT_APP_COMMON_SERVER_ADDRESS,
            { query, variables },
            {
                timeout: 1000,
                headers: {
                    "Authorization": `Bearer ${ store.getState().auth.token.token }`,
                    "Accept": "application/json",
                }
            }
        );
        if (response.data.errors && response.data.errors[0]?.state === 401) {
            const oldToken = store.getState().auth.token.token
            store.dispatch(refresh({ refreshToken: store.getState().auth.token.refreshToken }))
            let resolve
            const promise = new Promise((rs, rj) => {resolve = rs})
            const unsubscribe = store.subscribe(async () => {
                if (store.getState().auth.token.token === oldToken) {
                    return
                }
                unsubscribe()
                const repeatResponse = await axios.post(process.env.REACT_APP_COMMON_SERVER_ADDRESS,
                    { query, variables },
                    {
                        timeout: 1000,
                        headers: {
                            "Authorization": `Bearer ${ store.getState().auth.token.token }`,
                            "Accept": "application/json",
                        }
                    }
                );
                resolve(repeatResponse)
            });
            return promise
        }
        return Promise.resolve(response)
    }
} 

export default client