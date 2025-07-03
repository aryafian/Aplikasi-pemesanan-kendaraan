import { apiRequest } from "./queryClient";

export interface User {
  id: number;
  username: string;
  fullName: string;
  role: string;
  department?: string;
  approvalLevel?: string;
}

export const auth = {
  async login(username: string, password: string): Promise<User> {
    const response = await apiRequest('POST', '/api/auth/login', { username, password });
    const data = await response.json();
    return data.user;
  },

  async logout(): Promise<void> {
    await apiRequest('POST', '/api/auth/logout');
  },

  async getCurrentUser(): Promise<User | null> {
    try {
      const response = await apiRequest('GET', '/api/auth/me');
      const data = await response.json();
      return data.user;
    } catch (error) {
      return null;
    }
  },

  isAdmin(user: User): boolean {
    return user.role === 'admin';
  },

  isApprover(user: User): boolean {
    return user.role === 'approver';
  },

  isRequester(user: User): boolean {
    return user.role === 'requester';
  },

  canApprove(user: User): boolean {
    return user.role === 'admin' || user.role === 'approver';
  },

  canManageVehicles(user: User): boolean {
    return user.role === 'admin';
  },

  canManageDrivers(user: User): boolean {
    return user.role === 'admin';
  },

  canViewReports(user: User): boolean {
    return user.role === 'admin' || user.role === 'approver';
  },
};
