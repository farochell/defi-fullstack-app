import axios, { type AxiosInstance, type AxiosError } from 'axios'
import type { Router } from 'vue-router'
import type {
  AnalyticDistanceRequest,
  AnalyticDistanceResponse,
  ApiError,
  CreateUserRequest, CreateUserResponse,
  DistanceRequest,
  DistanceResponse,
  LoginRequest,
  LoginResponse,
} from '../types/api.ts'

class ApiService {
  private api: AxiosInstance
  private router: Router | null = null
  private readonly TOKEN_KEY = 'auth_token'

  constructor() {
    this.api = axios.create({
      baseURL: import.meta.env.VITE_API_BASE_URL,
      headers: {
        'Content-Type': 'application/json',
      },
    })

    this.initializeAuth()
    this.setupInterceptors()
  }

  setRouter(router: Router): void {
    this.router = router
  }

  private initializeAuth(): void {
    const token = this.getToken()
    if (token) {
      this.setAuthHeader(token)
    }
  }

  private setupInterceptors(): void {
    this.api.interceptors.response.use(
      (response) => response,
      (error) => {
        if (error.response?.status === 401 || error.response?.status === 403) {
          this.handleUnauthorized()
        }
        return Promise.reject(error)
      }
    )
  }

  private handleUnauthorized(): void {
    this.logout()
    this.redirectToHome()
  }

  private redirectToHome(): void {
    if (this.router) {
      this.router.push('/').catch(() => {
        window.location.href = '/'
      })
    } else if (typeof window !== 'undefined') {
      window.location.href = '/'
    }
  }

  private setAuthHeader(token: string): void {
    this.api.defaults.headers.common['Authorization'] = `Bearer ${token}`
  }

  private handleError(error: unknown, defaultMessage: string, defaultCode: string): ApiError {
    if (axios.isAxiosError(error)) {
      const axiosError = error as AxiosError<any>

      if (axiosError.response?.data) {
        return {
          code: axiosError.response.data.code ?? defaultCode,
          message: axiosError.response.data.message ?? defaultMessage,
          details: axiosError.response.data.details,
        }
      }

      if (axiosError.request) {
        return {
          code: 'NO_RESPONSE',
          message: 'Aucune réponse du serveur',
        }
      }

      return {
        code: 'AXIOS_ERROR',
        message: axiosError.message,
      }
    }

    return {
      code: 'UNKNOWN_ERROR',
      message: 'Une erreur inattendue est survenue',
    }
  }

  async calculateDistance(data: DistanceRequest): Promise<{ data?: DistanceResponse; error?: ApiError }> {
    try {
      const response = await this.api.post<DistanceResponse>('/api/v1/routes', data)
      return { data: response.data }
    } catch (error: unknown) {
      return {
        error: this.handleError(error, 'Une erreur est survenue', 'UNKNOWN_ERROR'),
      }
    }
  }

  async login(data: LoginRequest): Promise<{ data?: LoginResponse; error?: ApiError }> {
    try {
      const response = await this.api.post<LoginResponse>('/api/login', data)

      if (response.data.token) {
        localStorage.setItem(this.TOKEN_KEY, response.data.token)
        this.setAuthHeader(response.data.token)
      }

      return { data: response.data }
    } catch (error: unknown) {
      return {
        error: this.handleError(error, 'Email ou mot de passe incorrect', 'UNAUTHORIZED'),
      }
    }
  }

  async createUser(data: CreateUserRequest): Promise<{ data?: CreateUserResponse; error?: ApiError }> {
    try {
      const response = await this.api.post<CreateUserRequest>('/api/users/create', data)
      return { data: response.data }
    } catch (error: unknown) {
      return {
        error: this.handleError(
          error,
          "Erreur lors de la création de l'utilisateur",
          'CREATE_USER_ERROR',
        ),
      }
    }
  }

  async getStats(data: AnalyticDistanceRequest): Promise<{ data?: AnalyticDistanceResponse; error?: ApiError }> {
    try {
      const params = new URLSearchParams()
      if (data.from) params.append('from', data.from)
      if (data.to) params.append('to', data.to)
      if (data.groupBy) params.append('groupBy', data.groupBy)

      const response = await this.api.get<AnalyticDistanceResponse>(
        `/api/v1/stats/distances?${params.toString()}`
      )

      return { data: response.data }
    } catch (error: unknown) {
      return {
        error: this.handleError(error, 'Erreur lors de la récupération des statistiques', 'STATS_ERROR'),
      }
    }
  }

  logout(): void {
    localStorage.removeItem(this.TOKEN_KEY)
    delete this.api.defaults.headers.common['Authorization']
  }

  isAuthenticated(): boolean {
    return !!this.getToken()
  }

  getToken(): string | null {
    return localStorage.getItem(this.TOKEN_KEY)
  }
}

export const apiService = new ApiService()
