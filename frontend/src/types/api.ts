export interface DistanceRequest {
  fromStationId: string
  toStationId: string
  analyticCode: string
}

export interface Station {
  id: string
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
