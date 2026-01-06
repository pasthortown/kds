import axios from 'axios';

const API_URL = import.meta.env.VITE_API_URL || '/api';

const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Interceptor para agregar token
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('accessToken');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Interceptor para manejar errores de autenticación
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    const originalRequest = error.config;

    // No intentar refresh en endpoints de auth (evita loop infinito)
    const isAuthEndpoint = originalRequest.url?.includes('/auth/');

    if (error.response?.status === 401 && !originalRequest._retry && !isAuthEndpoint) {
      originalRequest._retry = true;

      const refreshToken = localStorage.getItem('refreshToken');
      if (refreshToken) {
        try {
          const { data } = await api.post('/auth/refresh', {
            refreshToken,
          });
          localStorage.setItem('accessToken', data.accessToken);
          originalRequest.headers.Authorization = `Bearer ${data.accessToken}`;
          return api(originalRequest);
        } catch {
          localStorage.removeItem('accessToken');
          localStorage.removeItem('refreshToken');
          window.location.href = '/login';
          return Promise.reject(error);
        }
      } else {
        // No hay refreshToken, ir a login
        localStorage.removeItem('accessToken');
        window.location.href = '/login';
        return Promise.reject(error);
      }
    }

    // Si es 401 en endpoint de auth, limpiar y redirigir
    if (error.response?.status === 401 && isAuthEndpoint) {
      localStorage.removeItem('accessToken');
      localStorage.removeItem('refreshToken');
      if (!window.location.pathname.includes('/login')) {
        window.location.href = '/login';
      }
    }

    return Promise.reject(error);
  }
);

// Auth
export const authApi = {
  login: (email: string, password: string) =>
    api.post('/auth/login', { email, password }),
  refresh: (refreshToken: string) =>
    api.post('/auth/refresh', { refreshToken }),
  me: () => api.get('/auth/me'),
  changePassword: (currentPassword: string, newPassword: string) =>
    api.post('/auth/change-password', { currentPassword, newPassword }),
};

// Screens
export const screensApi = {
  getAll: () => api.get('/screens'),
  get: (id: string) => api.get(`/screens/${id}`),
  create: (data: any) => api.post('/screens', data),
  update: (id: string, data: any) => api.put(`/screens/${id}`, data),
  delete: (id: string) => api.delete(`/screens/${id}`),
  getConfig: (id: string) => api.get(`/screens/${id}/config`),
  updateAppearance: (id: string, data: any) =>
    api.put(`/screens/${id}/appearance`, data),
  updateKeyboard: (id: string, data: any) =>
    api.put(`/screens/${id}/keyboard`, data),
  setStandby: (id: string) => api.post(`/screens/${id}/standby`),
  activate: (id: string) => api.post(`/screens/${id}/activate`),
  regenerateKey: (id: string) => api.post(`/screens/${id}/regenerate-key`),
  updatePreference: (id: string, data: any) =>
    api.put(`/screens/${id}/preference`, data),
  updatePrinter: (id: string, data: any) =>
    api.put(`/screens/${id}/printer`, data),
  deletePrinter: (id: string) => api.delete(`/screens/${id}/printer`),
  testPrinter: (id: string) => api.post(`/screens/${id}/printer/test`),
  copyAppearanceFrom: (targetId: string, sourceId: string) =>
    api.post(`/screens/${targetId}/copy-appearance-from/${sourceId}`),
};

// Queues
export const queuesApi = {
  getAll: () => api.get('/queues'),
  get: (id: string) => api.get(`/queues/${id}`),
  create: (data: any) => api.post('/queues', data),
  update: (id: string, data: any) => api.put(`/queues/${id}`, data),
  delete: (id: string) => api.delete(`/queues/${id}`),
  addChannel: (queueId: string, data: any) =>
    api.post(`/queues/${queueId}/channels`, data),
  updateChannel: (queueId: string, channelId: string, data: any) =>
    api.put(`/queues/${queueId}/channels/${channelId}`, data),
  deleteChannel: (queueId: string, channelId: string) =>
    api.delete(`/queues/${queueId}/channels/${channelId}`),
  addFilter: (queueId: string, data: any) =>
    api.post(`/queues/${queueId}/filters`, data),
  deleteFilter: (queueId: string, filterId: string) =>
    api.delete(`/queues/${queueId}/filters/${filterId}`),
  getStats: (id: string) => api.get(`/queues/${id}/stats`),
  resetBalance: (id: string) => api.post(`/queues/${id}/reset-balance`),
};

// Orders
export const ordersApi = {
  getAll: (params?: any) => api.get('/orders', { params }),
  get: (id: string) => api.get(`/orders/${id}`),
  getByScreen: (screenId: string) => api.get(`/orders/screen/${screenId}`),
  finish: (id: string, screenId: string) =>
    api.post(`/orders/${id}/finish`, { screenId }),
  undo: (id: string) => api.post(`/orders/${id}/undo`),
  cancel: (id: string, reason?: string) =>
    api.post(`/orders/${id}/cancel`, { reason }),
  getStats: () => api.get('/orders/stats'),
  getDashboardStats: (timeLimit?: number) =>
    api.get('/orders/dashboard-stats', { params: { timeLimit } }),
  cleanup: (hours?: number) => api.delete('/orders/cleanup', { params: { hours } }),
  generateTest: (count: number, includeLong?: boolean) =>
    api.post('/orders/generate-test', { count, includeLong }),
  deleteTestOrders: () => api.delete('/orders/test-orders'),
};

// Users
export const usersApi = {
  getAll: () => api.get('/users'),
  get: (id: string) => api.get(`/users/${id}`),
  create: (data: { email: string; password: string; name: string; role: string }) =>
    api.post('/users', data),
  update: (id: string, data: any) => api.put(`/users/${id}`, data),
  delete: (id: string) => api.delete(`/users/${id}`),
  toggleActive: (id: string) => api.post(`/users/${id}/toggle-active`),
};

// Config
export const configApi = {
  getGeneral: () => api.get('/config/general'),
  updateGeneral: (data: any) => api.put('/config/general', data),
  getMxp: () => api.get('/config/mxp'),
  updateMxp: (data: any) => api.put('/config/mxp', data),
  testMxpConnection: (data: {
    mxpHost: string;
    mxpPort?: number;
    mxpUser: string;
    mxpPassword: string;
    mxpDatabase: string;
  }) => api.post('/config/mxp/test', data),
  getPollingStatus: () => api.get('/config/polling'),
  startPolling: () => api.post('/config/polling/start'),
  stopPolling: () => api.post('/config/polling/stop'),
  forcePoll: () => api.post('/config/polling/force'),
  health: () => api.get('/config/health'),
  stats: () => api.get('/config/stats'),
  // Modos de configuración (Tickets e Impresión)
  getModes: () => api.get('/config/modes'),
  updateModes: (data: {
    ticketMode?: 'POLLING' | 'API';
    printMode?: 'LOCAL' | 'CENTRALIZED';
    centralizedPrintUrl?: string;
    centralizedPrintUrlBackup?: string;
    centralizedPrintPort?: number;
    printTemplate?: string;
    printTemplateType?: string;
    screenPrinters?: Array<{ id: string; printerName: string }>;
  }) => api.put('/config/modes', data),
  testCentralizedPrint: (useBackup?: boolean) =>
    api.post(`/config/print/test-centralized${useBackup ? '?backup=true' : ''}`),
};

// Mirror KDS - DESACTIVADO
// El modo mirror ha sido deshabilitado permanentemente
export const mirrorApi = {
  // eslint-disable-next-line @typescript-eslint/no-unused-vars
  configure: (_data: { host: string; port?: number; user: string; password: string; database: string }) =>
    Promise.resolve({ data: { success: false, message: 'Modo mirror deshabilitado permanentemente' } }),
  test: () => Promise.resolve({ data: { success: false, message: 'Modo mirror deshabilitado' } }),
  stats: () => Promise.resolve({ data: { connected: false, ordersOnScreen: 0, screens: [] as string[], queues: [] as string[], mode: 'local-postgresql' as const } }),
  // eslint-disable-next-line @typescript-eslint/no-unused-vars
  getOrders: (_params?: { screen?: string; queue?: string }) =>
    Promise.resolve({ data: { success: true, orders: [], total: 0 } }),
  getScreens: () => Promise.resolve({ data: { success: true, screens: [] as string[] } }),
  getQueues: () => Promise.resolve({ data: { success: true, queues: [] as string[] } }),
  disconnect: () => Promise.resolve({ data: { success: true, message: 'Desconectado' } }),
};

// Reports (Reportes del Dashboard)
export const reportsApi = {
  // Obtiene todos los datos del dashboard para reportes (JSON)
  getDashboardReport: (timeLimit?: number) =>
    api.get('/reports/dashboard', { params: { timeLimit } }),
  // Obtiene resumen diario (JSON)
  getDailySummary: (date?: string) =>
    api.get('/reports/daily-summary', { params: { date } }),
};

// Channels (Configuración de canales)
export const channelsApi = {
  getAll: () => api.get('/channels'),
  getActive: () => api.get('/channels/active'),
  get: (id: string) => api.get(`/channels/${id}`),
  getByName: (name: string) => api.get(`/channels/by-name/${name}`),
  create: (data: {
    name: string;
    displayName?: string;
    backgroundColor: string;
    textColor: string;
    icon?: string;
    priority?: number;
    active?: boolean;
  }) => api.post('/channels', data),
  update: (id: string, data: {
    name?: string;
    displayName?: string;
    backgroundColor?: string;
    textColor?: string;
    icon?: string;
    priority?: number;
    active?: boolean;
  }) => api.put(`/channels/${id}`, data),
  delete: (id: string) => api.delete(`/channels/${id}`),
  seed: () => api.post('/channels/seed'),
};

export default api;
