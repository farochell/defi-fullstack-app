import { describe, it, expect, vi, beforeEach, afterEach, type Mock } from "vitest"
import axios, { type AxiosInstance } from 'axios'
import type { Router } from "vue-router"

interface MockAxiosInstance extends Partial<AxiosInstance> {
  post: Mock
  get: Mock
  defaults: {
    headers: {
      common: Record<string, string | undefined>
    }
  }
  interceptors: {
    response: {
      use: Mock
    }
  }
}

vi.mock("axios")

const localStorageMock = (() => {
  let store: Record<string, string> = {}
  return {
    getItem: (key: string) => store[key] || null,
    setItem: (key: string, value: string) => {
      store[key] = value
    },
    removeItem: (key: string) => {
      delete store[key]
    },
    clear: () => {
      store = {}
    },
  }
})()

Object.defineProperty(window, "localStorage", {
  value: localStorageMock,
})

describe("ApiService", () => {
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  let ApiService: new () => any
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  let apiService: any
  let mockAxiosInstance: MockAxiosInstance
  let mockRouter: Partial<Router>
  let baseGet: Mock
  let basePost: Mock

  beforeEach(async () => {
    vi.clearAllMocks()
    localStorageMock.clear()

    let errorInterceptor: ((error: unknown) => Promise<void>) | null = null
    baseGet = vi.fn()
    basePost = vi.fn()

    mockAxiosInstance = {
      post: basePost,
      get: baseGet,
      defaults: {
        headers: {
          common: {},
        },
      },
      interceptors: {
        response: {
          use: vi.fn((onSuccess, onError) => {
            errorInterceptor = onError
            return 0
          }),
        },
      },
    }

    mockAxiosInstance.get = vi.fn(async (...args) => {
      try {
        return await baseGet(...args)
      } catch (error) {
        if (errorInterceptor) {
          await errorInterceptor(error)
        }
        throw error
      }
    })

    mockAxiosInstance.post = vi.fn(async (...args) => {
      try {
        return await basePost(...args)
      } catch (error) {
        if (errorInterceptor) {
          await errorInterceptor(error)
        }
        throw error
      }
    })

    vi.mocked(axios.create).mockReturnValue(mockAxiosInstance as unknown as AxiosInstance)
    vi.mocked(axios.isAxiosError).mockImplementation((error: unknown) => {
      return !!error && typeof error === 'object' && 'isAxiosError' in error && error.isAxiosError === true
    })

    mockRouter = {
      push: vi.fn().mockResolvedValue(undefined),
    }

    const module = await import("../services/api")
    ApiService = module.apiService.constructor
    apiService = new ApiService()
  })

  afterEach(() => {
    vi.clearAllMocks()
  })

  describe("constructeur", () => {
    it("crée une instance axios avec la configuration correcte", () => {
      expect(axios.create).toHaveBeenCalledWith({
        baseURL: import.meta.env.VITE_API_BASE_URL,
        headers: {
          "Content-Type": "application/json",
        },
      })
    })

    it("charge le token depuis localStorage lors de l'initialisation", () => {
      localStorageMock.setItem("auth_token", "test-token")
      const service = new ApiService()
      expect(service).toBeDefined()
    })

    it("configure l'intercepteur de réponse", () => {
      expect(mockAxiosInstance.interceptors.response.use).toHaveBeenCalled()
    })
  })

  describe("setRouter", () => {
    it("définit l'instance du routeur", () => {
      apiService.setRouter(mockRouter)
      expect(apiService).toBeDefined()
    })
  })

  describe("calculateDistance", () => {
    it("effectue une requête POST vers /api/v1/routes", async () => {
      const mockResponse = {
        data: {
          id: "1",
          fromStationId: "ALLI",
          toStationId: "BCHX",
          distanceKm: 100,
          analyticCode: "fret",
          path: { stations: [] },
          createdAt: "2024-01-01",
        },
      }

      mockAxiosInstance.post.mockResolvedValue(mockResponse)

      const result = await apiService.calculateDistance({
        fromStationId: "ALLI",
        toStationId: "BCHX",
        analyticCode: "fret",
      })

      expect(mockAxiosInstance.post).toHaveBeenCalledWith("/api/v1/routes", {
        fromStationId: "ALLI",
        toStationId: "BCHX",
        analyticCode: "fret",
      })
      expect(result.data).toEqual(mockResponse.data)
      expect(result.error).toBeUndefined()
    })

    it("gère les réponses d'erreur de l'API", async () => {
      const mockError = {
        isAxiosError: true,
        response: {
          data: {
            code: "ROUTE_NOT_FOUND",
            message: "Route not found",
          },
        },
      }

      mockAxiosInstance.post.mockRejectedValue(mockError)

      const result = await apiService.calculateDistance({
        fromStationId: "INVALID",
        toStationId: "INVALID2",
        analyticCode: "fret",
      })

      expect(result.data).toBeUndefined()
      expect(result.error).toEqual({
        code: "ROUTE_NOT_FOUND",
        message: "Route not found",
        details: undefined,
      })
    })

    it("gère les erreurs réseau", async () => {
      const mockError = {
        isAxiosError: true,
        request: {},
      }

      mockAxiosInstance.post.mockRejectedValue(mockError)

      const result = await apiService.calculateDistance({
        fromStationId: "ALLI",
        toStationId: "BCHX",
        analyticCode: "fret",
      })

      expect(result.error).toEqual({
        code: "NO_RESPONSE",
        message: "Aucune réponse du serveur",
      })
    })
  })

  describe("login", () => {
    it("effectue une requête POST vers /api/login", async () => {
      const mockResponse = {
        data: { token: "test-token" },
      }

      mockAxiosInstance.post.mockResolvedValue(mockResponse)

      const result = await apiService.login({
        email: "test@example.com",
        password: "password123",
      })

      expect(mockAxiosInstance.post).toHaveBeenCalledWith("/api/login", {
        email: "test@example.com",
        password: "password123",
      })
      expect(result.data).toEqual({ token: "test-token" })
    })

    it("stocke le token dans localStorage lors d'une connexion réussie", async () => {
      const mockResponse = {
        data: { token: "test-token" },
      }

      mockAxiosInstance.post.mockResolvedValue(mockResponse)

      await apiService.login({
        email: "test@example.com",
        password: "password123",
      })

      expect(localStorageMock.getItem("auth_token")).toBe("test-token")
    })

    it("définit l'en-tête Authorization lors d'une connexion réussie", async () => {
      const mockResponse = {
        data: { token: "test-token" },
      }

      mockAxiosInstance.post.mockResolvedValue(mockResponse)

      await apiService.login({
        email: "test@example.com",
        password: "password123",
      })

      expect(mockAxiosInstance.defaults.headers.common["Authorization"]).toBe("Bearer test-token")
    })

    it("gère l'échec de connexion", async () => {
      const mockError = {
        isAxiosError: true,
        response: {
          data: {
            code: "UNAUTHORIZED",
            message: "Email ou mot de passe incorrect",
          },
        },
      }

      mockAxiosInstance.post.mockRejectedValue(mockError)

      const result = await apiService.login({
        email: "test@example.com",
        password: "wrongpassword",
      })

      expect(result.error).toEqual({
        code: "UNAUTHORIZED",
        message: "Email ou mot de passe incorrect",
        details: undefined,
      })
    })
  })

  describe("getStats", () => {
    it("effectue une requête GET vers /api/stats/distances", async () => {
      const mockResponse = {
        data: {
          from: "2024-01-01",
          to: "2024-12-31",
          groupBy: "month",
          items: [],
        },
      }

      mockAxiosInstance.get.mockResolvedValue(mockResponse)
      localStorageMock.setItem("auth_token", "test-token")

      const result = await apiService.getStats({
        from: "2024-01-01",
        to: "2024-12-31",
        groupBy: "month",
      })

      expect(mockAxiosInstance.get).toHaveBeenCalled()
      expect(result.data).toEqual(mockResponse.data)
    })

    it("inclut les paramètres de requête dans la requête", async () => {
      const mockResponse = { data: { from: "", to: "", groupBy: "none", items: [] } }
      mockAxiosInstance.get.mockResolvedValue(mockResponse)
      localStorageMock.setItem("auth_token", "test-token")

      await apiService.getStats({
        from: "2024-01-01",
        to: "2024-12-31",
        groupBy: "day",
      })

      const callArgs = mockAxiosInstance.get.mock.calls[0]
      expect(callArgs[0]).toContain("from=2024-01-01")
      expect(callArgs[0]).toContain("to=2024-12-31")
      expect(callArgs[0]).toContain("groupBy=day")
    })

    it("inclut l'en-tête Authorization avec le token bearer", async () => {
      const mockResponse = { data: { from: "", to: "", groupBy: "none", items: [] } }
      mockAxiosInstance.get.mockResolvedValue(mockResponse)
      localStorageMock.setItem("auth_token", "test-token")

      const service = new ApiService()
      await service.getStats({})

      expect(mockAxiosInstance.defaults.headers.common["Authorization"]).toBe("Bearer test-token")
    })

    it("gère l'erreur 401 en effaçant le token et en redirigeant", async () => {
      const mockError = {
        isAxiosError: true,
        response: {
          status: 401,
          data: {
            code: "UNAUTHORIZED",
            message: "Token expired",
          },
        },
      }

      baseGet.mockRejectedValue(mockError)
      localStorageMock.setItem("auth_token", "test-token")
      apiService.setRouter(mockRouter)

      await apiService.getStats({})

      expect(localStorageMock.getItem("auth_token")).toBeNull()
      expect(mockRouter.push).toHaveBeenCalledWith("/")
    })

    it("gère l'erreur 403 en effaçant le token et en redirigeant", async () => {
      const mockError = {
        isAxiosError: true,
        response: {
          status: 403,
          data: {
            code: "FORBIDDEN",
            message: "Access forbidden",
          },
        },
      }

      baseGet.mockRejectedValue(mockError)
      localStorageMock.setItem("auth_token", "test-token")
      apiService.setRouter(mockRouter)

      await apiService.getStats({})

      expect(localStorageMock.getItem("auth_token")).toBeNull()
      expect(mockRouter.push).toHaveBeenCalledWith("/")
    })
  })

  describe("logout", () => {
    it("supprime le token de localStorage", () => {
      localStorageMock.setItem("auth_token", "test-token")
      apiService.logout()
      expect(localStorageMock.getItem("auth_token")).toBeNull()
    })

    it("supprime l'en-tête Authorization", () => {
      mockAxiosInstance.defaults.headers.common["Authorization"] = "Bearer test-token"
      apiService.logout()
      expect(mockAxiosInstance.defaults.headers.common["Authorization"]).toBeUndefined()
    })
  })

  describe("isAuthenticated", () => {
    it("retourne true lorsque le token existe", () => {
      localStorageMock.setItem("auth_token", "test-token")
      expect(apiService.isAuthenticated()).toBe(true)
    })

    it("retourne false lorsque le token n'existe pas", () => {
      localStorageMock.removeItem("auth_token")
      expect(apiService.isAuthenticated()).toBe(false)
    })
  })

  describe("getToken", () => {
    it("retourne le token lorsqu'il existe", () => {
      localStorageMock.setItem("auth_token", "test-token")
      expect(apiService.getToken()).toBe("test-token")
    })

    it("retourne null lorsque le token n'existe pas", () => {
      localStorageMock.removeItem("auth_token")
      expect(apiService.getToken()).toBeNull()
    })
  })
})
