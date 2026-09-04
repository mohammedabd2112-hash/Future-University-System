export interface User {
  id: number;
  name: string;
  email: string;
  role: 'admin' | 'student';
}

export interface LoginResponse {
  success: boolean;
  message: string;
  user?: User;
}
