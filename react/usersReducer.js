import { LOGOUT, ADMIN_USERS_SUCCESS } from '../../actions/types';

const initialState = {
  entities: {},
  data: [],
  perPage: 10,
  page: 1,
  total: 0,
}

export default function usersReducer(state = initialState, action) {
  switch (action.type) {
    case LOGOUT:
      return initialState
    case ADMIN_USERS_SUCCESS:
      return { ...state,
        entities: {
          ...state.entities,
          ...action.payload.adminUsers.data.reduce((acc, item) => {
            acc[item.id] = item
            return acc
          }, {})
        },
        data: action.payload.adminUsers.data.map(item => item.id),
        perPage: action.payload.adminUsers.perPage,
        page: action.payload.adminUsers.page,
        total: action.payload.adminUsers.total }
    default:
      return state;
  }
}