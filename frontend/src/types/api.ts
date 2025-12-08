export interface DistanceRequest {
  fromStationId: string
  toStationId: string
  analyticCode: string
}

export interface Station {
  id: number
  shortName: string
  longName: string
}

export interface Path {
  stations: Station[]
}

export interface DistanceResponse {
  id: string
  fromStationId: string
  toStationId: string
  distanceKm: number
  analyticCode: string
  path: Path
  createdAt: string
}

export interface ApiError {
  code: string;
  message: string;
  details?: string[];
}

export interface LoginRequest {
  email: string
  password: string
}

export interface CreateUserRequest {
  email: string
  password: string
}

export interface CreateUserResponse {
  id: string
  email: string
}

export interface LoginResponse {
  token: string
}

export interface AnalyticDistanceRequest {
  from?: string
  to?: string
  groupBy?: 'day' | 'month' | 'year' | 'none'
}

export interface AnalyticDistance {
  analyticCode: 'fret' | 'passager'
  totalDistanceKm: number
  periodStart: string
  periodEnd: string
  group?: string|null
}

export interface AnalyticDistanceResponse {
  from: string
  to: string
  groupBy: 'day' | 'month' | 'year' | 'none'
  items: AnalyticDistance[]
}
