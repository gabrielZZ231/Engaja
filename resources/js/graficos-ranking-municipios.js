import ApexCharts from "apexcharts";
import { getGraficoPadraoConfig } from "./grafico-template";

const CHART_SELECTOR = '[data-chart="ranking-municipios"]';
const chartsPorElemento = new Map();
let hooksLivewireRegistrados = false;

const formatadorAbsoluto = new Intl.NumberFormat("pt-BR", {
    minimumFractionDigits: 0,
    maximumFractionDigits: 2,
});

const obterCorPadrao = () =>
    getComputedStyle(document.documentElement)
        .getPropertyValue("--engaja-purple")
        .trim() || "#421944";

const parseDados = (dadosBrutos) => {
    if (!dadosBrutos) {
        return [];
    }

    try {
        const dados = JSON.parse(dadosBrutos);
        if (!Array.isArray(dados)) {
            return [];
        }

        return dados
            .map((item) => ({
                municipio: String(item?.municipio ?? "").trim(),
                valor: Number(item?.valor),
            }))
            .filter((item) => item.municipio && Number.isFinite(item.valor));
    } catch (error) {
        console.error("Erro ao processar dados do grafico de ranking:", error);
        return [];
    }
};

const limparGraficosOrfaos = () => {
    for (const [elemento, chart] of chartsPorElemento.entries()) {
        if (!document.body.contains(elemento)) {
            chart.destroy();
            chartsPorElemento.delete(elemento);
        }
    }
};

const renderizarGrafico = (elemento) => {
    const dados = parseDados(elemento.dataset.dados);
    if (!dados.length) {
        const chartAtual = chartsPorElemento.get(elemento);
        if (chartAtual) {
            chartAtual.destroy();
            chartsPorElemento.delete(elemento);
        }

        delete elemento.dataset.chartAssinatura;
        return;
    }

    const titulo = elemento.dataset.titulo?.trim() || "Ranking de Municipios";
    const tipoValor = (elemento.dataset.tipoValor || "PERCENTUAL").toUpperCase();
    const assinaturaDados = `${elemento.dataset.dados || ""}|${titulo}|${tipoValor}`;

    if (elemento.dataset.chartAssinatura === assinaturaDados) {
        return;
    }

    elemento.dataset.chartAssinatura = assinaturaDados;

    const chartAtual = chartsPorElemento.get(elemento);
    if (chartAtual) {
        chartAtual.destroy();
        chartsPorElemento.delete(elemento);
    }

    const percentual = tipoValor === "PERCENTUAL";
    const formatarValor = percentual
        ? (valor) => `${Number(valor).toFixed(2)}%`
        : (valor) => formatadorAbsoluto.format(Number(valor));

    const config = getGraficoPadraoConfig({
        titulo,
        categorias: dados.map((item) => item.municipio),
        valores: dados.map((item) => item.valor),
        cor: obterCorPadrao(),
        altura: 500,
        tipo: "bar",
        horizontal: true,
        nomeSerie: percentual ? "Valor (%)" : "Valor",
        formatarValor,
    });

    const chart = new ApexCharts(elemento, config);
    chart.render();
    chartsPorElemento.set(elemento, chart);
};

const inicializarGraficosRanking = () => {
    limparGraficosOrfaos();
    document.querySelectorAll(CHART_SELECTOR).forEach(renderizarGrafico);
};

const registrarHooksLivewire = () => {
    if (hooksLivewireRegistrados || !window.Livewire?.hook) {
        return;
    }

    hooksLivewireRegistrados = true;

    const hook = window.Livewire.hook.bind(window.Livewire);
    ["message.processed", "morph.updated", "commit"].forEach((nomeHook) => {
        try {
            hook(nomeHook, () => queueMicrotask(inicializarGraficosRanking));
        } catch {
            // Ignora hooks indisponiveis na versao atual do Livewire
        }
    });
};

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", inicializarGraficosRanking, {
        once: true,
    });
} else {
    inicializarGraficosRanking();
}

document.addEventListener("livewire:init", () => {
    registrarHooksLivewire();
    inicializarGraficosRanking();
});

document.addEventListener("livewire:initialized", () => {
    registrarHooksLivewire();
    inicializarGraficosRanking();
});

document.addEventListener("livewire:navigated", inicializarGraficosRanking);
