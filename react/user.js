import { fetchGenerator } from '../../utils/fetchGenerator';

export const fetchAdminUsers = fetchGenerator(`
  query adminUsers($filter: AdminUsersFilter, $paging: DataPage, $sorting: AdminUserSort) {
    adminUsers(filter: $filter, paging: $paging, sorting: $sorting){
      total
      page,
      perPage
      data {
        id,
        created_at,
        username,
        type
      }
    }
  }`, 'admin_users')
