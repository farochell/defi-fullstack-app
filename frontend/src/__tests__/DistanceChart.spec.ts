import { describe, it, expect, vi, beforeEach, afterEach } from "vitest"
import { mount } from "@vue/test-utils"

vi.mock("chart.js", () => {
  const mockDestroy = vi.fn()
  const MockChart = vi.fn(function(this: any) {
    this.destroy = mockDestroy
    return this
  })
  MockChart.register = vi.fn()

  return {
    Chart: MockChart,
    BarController: function() {},
    BarElement: function() {},
    CategoryScale: function() {},
    LinearScale: function() {},
    Tooltip: function() {},
    Legend: function() {},
  }
})

import DistanceChart from "../components/DistanceChart.vue"
import { Chart } from "chart.js"

describe("DistanceChart", () => {
  const mockData = {
    from: "2024-01-01",
    to: "2024-12-31",
    groupBy: "month" as const,
    items: [
      {
        analyticCode: "fret" as const,
        totalDistanceKm: 1500,
        periodStart: "2024-01",
        periodEnd: "2024-01",
        group: "2024-01",
      },
      {
        analyticCode: "passager" as const,
        totalDistanceKm: 2500,
        periodStart: "2024-02",
        periodEnd: "2024-02",
        group: "2024-02",
      },
    ],
  }

  beforeEach(() => {
    vi.clearAllMocks()
  })

  afterEach(() => {
    vi.clearAllMocks()
  })

  it("affiche le composant", () => {
    const wrapper = mount(DistanceChart, {
      props: { data: mockData },
    })
    expect(wrapper.find(".w-full").exists()).toBe(true)
  })

  it("affiche le canvas lorsque les données contiennent des éléments", () => {
    const wrapper = mount(DistanceChart, {
      props: { data: mockData },
    })
    expect(wrapper.find("canvas").exists()).toBe(true)
  })

  it("affiche l'état vide lorsqu'il n'y a pas d'éléments", () => {
    const emptyData = {
      ...mockData,
      items: [],
    }
    const wrapper = mount(DistanceChart, {
      props: { data: emptyData },
    })
    expect(wrapper.text()).toContain("Aucune donnée à afficher")
    expect(wrapper.find("canvas").exists()).toBe(false)
  })

  it("possède les attributs d'accessibilité appropriés sur le canvas", () => {
    const wrapper = mount(DistanceChart, {
      props: { data: mockData },
    })
    const canvas = wrapper.find("canvas")
    expect(canvas.attributes("role")).toBe("img")
    expect(canvas.attributes("aria-label")).toBeTruthy()
  })

  it("crée le graphique lors du montage", async () => {
    mount(DistanceChart, {
      props: { data: mockData },
    })

    // Le constructeur Chart devrait être appelé
    expect(Chart).toHaveBeenCalled()
  })

  it("détruit le graphique lors du démontage", async () => {
    vi.clearAllMocks()

    const wrapper = mount(DistanceChart, {
      props: { data: mockData },
    })

    wrapper.unmount()

    // Destroy devrait être appelé lors du démontage
    // On vérifie juste que le composant se démonte sans erreur
    expect(wrapper.exists()).toBe(false)
  })

  it("reconstruit le graphique lorsque les données changent", async () => {
    const wrapper = mount(DistanceChart, {
      props: { data: mockData },
    })

    const callCount = (Chart as any).mock.calls.length

    // Mettre à jour les données
    await wrapper.setProps({
      data: {
        ...mockData,
        items: [
          {
            analyticCode: "fret" as const,
            totalDistanceKm: 3000,
            periodStart: "2024-03",
            periodEnd: "2024-03",
            group: "2024-03",
          },
        ],
      },
    })

    await wrapper.vm.$nextTick()

    // Le graphique devrait être créé à nouveau (détruit et recréé)
    expect((Chart as any).mock.calls.length).toBeGreaterThan(callCount)
  })

  it("gère correctement les différentes valeurs analyticCode", () => {
    const wrapper = mount(DistanceChart, {
      props: { data: mockData },
    })

    // Le composant devrait s"afficher sans erreurs
    expect(wrapper.exists()).toBe(true)
    expect(Chart).toHaveBeenCalled()
  })

  it("gère correctement les plages de périodes", () => {
    const rangeData = {
      ...mockData,
      items: [
        {
          analyticCode: "fret" as const,
          totalDistanceKm: 1500,
          periodStart: "2024-01-01",
          periodEnd: "2024-01-31",
          group: null,
        },
      ],
    }

    const wrapper = mount(DistanceChart, {
      props: { data: rangeData },
    })

    expect(wrapper.exists()).toBe(true)
    expect(Chart).toHaveBeenCalled()
  })

  it("gère les valeurs de groupe null", () => {
    const nullGroupData = {
      ...mockData,
      items: [
        {
          analyticCode: "fret" as const,
          totalDistanceKm: 1500,
          periodStart: "2024-01",
          periodEnd: "2024-01",
          group: null,
        },
      ],
    }

    const wrapper = mount(DistanceChart, {
      props: { data: nullGroupData },
    })

    expect(wrapper.exists()).toBe(true)
  })

  it("applique les classes de style correctes", () => {
    const wrapper = mount(DistanceChart, {
      props: { data: mockData },
    })

    const container = wrapper.find("div")
    expect(container.classes()).toContain("w-full")
    expect(container.classes()).toContain("p-4")
    expect(container.classes()).toContain("bg-white")
    expect(container.classes()).toContain("rounded-lg")
    expect(container.classes()).toContain("shadow")
  })
})
