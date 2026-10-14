{{-- Estilos dedicados da Agenda de Contatos alinhados ao tema nativo Codhous (.cx-ui). Prefixo ca- --}}
<style>
    .ca-page {
        display: grid;
        gap: 20px;
        color: var(--cx-text);
        font-family: inherit;
    }

    /* Layout de duas colunas (Principal e Lateral) */
    .ca-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(310px, 360px);
        gap: 20px;
        align-items: start;
    }

    @media (max-width: 1080px) {
        .ca-layout {
            grid-template-columns: 1fr;
        }
    }

    /* Coluna Principal */
    .ca-main {
        display: flex;
        flex-direction: column;
        gap: 20px;
        min-width: 0;
    }

    /* Cards de Estatísticas do Topo (mesmo tom de Orçamentos e Visão geral) */
    .ca-kpis {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    @media (max-width: 840px) {
        .ca-kpis {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    .ca-kpi-card {
        background: var(--cx-surface);
        border: 1px solid var(--cx-border);
        border-radius: var(--cx-radius);
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        position: relative;
        box-shadow: var(--cx-shadow);
        transition: transform 0.15s ease, border-color 0.15s ease;
    }

    .ca-kpi-card:hover {
        border-color: var(--cx-border-strong);
    }

    .ca-kpi-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }

    .ca-kpi-icon.tone-blue {
        background: rgba(var(--primary-500), 0.12);
        color: rgb(var(--primary-400));
        box-shadow: inset 0 0 0 1px rgba(var(--primary-500), 0.16);
    }
    .ca-kpi-icon.tone-amber {
        background: rgba(var(--warning-500), 0.12);
        color: rgb(var(--warning-400));
        box-shadow: inset 0 0 0 1px rgba(var(--warning-500), 0.16);
    }
    .ca-kpi-icon.tone-purple {
        background: rgba(var(--purple-500), 0.12);
        color: rgb(var(--purple-400));
        box-shadow: inset 0 0 0 1px rgba(var(--purple-500), 0.16);
    }

    .ca-kpi-info {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .ca-kpi-label {
        font-size: 13px;
        color: var(--cx-muted);
        font-weight: 500;
        margin-bottom: 2px;
        white-space: nowrap;
    }

    .ca-kpi-value {
        font-size: 24px;
        font-weight: 700;
        color: var(--cx-text);
        line-height: 1.2;
        letter-spacing: -0.02em;
    }

    .ca-kpi-sub {
        font-size: 12px;
        color: var(--cx-subtle);
        margin-top: 4px;
        font-weight: 500;
    }

    .ca-kpi-sub.positive {
        color: rgb(var(--success-400));
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Abas de Filtro de Categoria */
    .ca-tabs {
        display: flex;
        align-items: center;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 4px;
    }

    .ca-tab {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: 9999px;
        font-size: 13px;
        font-weight: 500;
        color: var(--cx-muted);
        background: var(--cx-surface-2);
        border: 1px solid var(--cx-border);
        cursor: pointer;
        transition: all 0.15s ease;
        white-space: nowrap;
        user-select: none;
    }

    .ca-tab:hover {
        color: var(--cx-text);
        background: var(--cx-hover);
        border-color: var(--cx-border-strong);
    }

    .ca-tab.is-active {
        background: rgb(var(--primary-600));
        border-color: rgb(var(--primary-500));
        color: #ffffff;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(var(--primary-500), 0.3);
    }

    .ca-tab-count {
        font-size: 11px;
        padding: 1px 7px;
        border-radius: 9999px;
        background: rgba(255, 255, 255, 0.12);
        color: inherit;
        font-weight: 600;
    }

    .ca-tab.is-active .ca-tab-count {
        background: rgba(255, 255, 255, 0.25);
    }

    /* Barra de Pesquisa e Filtros */
    .ca-controls {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-top: 2px;
    }

    .ca-search-box {
        position: relative;
        flex: 1;
        max-width: 420px;
    }

    .ca-search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        width: 17px;
        height: 17px;
        color: var(--cx-subtle);
        pointer-events: none;
    }

    .ca-search-input {
        width: 100%;
        padding: 8px 16px 8px 40px;
        background: var(--cx-surface);
        border: 1px solid var(--cx-border);
        border-radius: 10px;
        font-size: 13px;
        color: var(--cx-text);
        transition: all 0.15s ease;
    }

    .ca-search-input:focus {
        outline: none;
        border-color: rgb(var(--primary-500));
        box-shadow: 0 0 0 3px rgba(var(--primary-500), 0.15);
    }

    .ca-search-input::placeholder {
        color: var(--cx-subtle);
    }

    .ca-actions-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .ca-btn-filter {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        background: var(--cx-surface);
        border: 1px solid var(--cx-border);
        border-radius: 10px;
        font-size: 13px;
        font-weight: 500;
        color: var(--cx-text);
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .ca-btn-filter:hover {
        background: var(--cx-hover);
        border-color: var(--cx-border-strong);
    }

    .ca-select-sort {
        padding: 8px 32px 8px 14px;
        background: var(--cx-surface);
        border: 1px solid var(--cx-border);
        border-radius: 10px;
        font-size: 13px;
        font-weight: 500;
        color: var(--cx-text);
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 15px;
        transition: all 0.15s ease;
    }

    .ca-select-sort:focus {
        outline: none;
        border-color: rgb(var(--primary-500));
    }

    /* Tabela de Contatos */
    .ca-table-wrap {
        background: var(--cx-surface);
        border: 1px solid var(--cx-border);
        border-radius: var(--cx-radius);
        overflow: hidden;
        box-shadow: var(--cx-shadow);
    }

    .ca-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        text-align: left;
        font-size: 13px;
    }

    .ca-table th {
        background: var(--cx-surface-2);
        color: var(--cx-muted);
        font-size: 12px;
        font-weight: 500;
        padding: 12px 16px;
        border-bottom: 1px solid var(--cx-border);
        white-space: nowrap;
        user-select: none;
    }

    .ca-table td {
        padding: 14px 16px;
        border-bottom: 1px solid var(--cx-border);
        vertical-align: middle;
        transition: background 0.12s ease;
    }

    .ca-table tbody tr:last-child td {
        border-bottom: none;
    }

    .ca-table tbody tr:hover td {
        background: var(--cx-hover);
    }

    .ca-checkbox {
        width: 16px;
        height: 16px;
        border-radius: 4px;
        border: 1px solid var(--cx-border-strong);
        background: var(--cx-surface-2);
        cursor: pointer;
        accent-color: rgb(var(--primary-600));
    }

    /* Coluna Contato (Avatar + Nome + Cargo) */
    .ca-contact-col {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .ca-avatar {
        width: 36px;
        height: 36px;
        border-radius: 9999px;
        display: grid;
        place-items: center;
        font-size: 12px;
        font-weight: 700;
        color: #ffffff;
        flex-shrink: 0;
        letter-spacing: 0.02em;
    }

    .ca-avatar-0 { background: #4f46e5; }
    .ca-avatar-1 { background: #6366f1; }
    .ca-avatar-2 { background: #2563eb; }
    .ca-avatar-3 { background: #0284c7; }
    .ca-avatar-4 { background: #7c3aed; }
    .ca-avatar-5 { background: #9333ea; }
    .ca-avatar-6 { background: #059669; }
    .ca-avatar-7 { background: #d97706; }
    .ca-avatar-8 { background: #db2777; }
    .ca-avatar-9 { background: #475569; }

    .ca-contact-meta {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .ca-contact-name {
        font-size: 14px;
        font-weight: 600;
        color: var(--cx-text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ca-contact-position {
        font-size: 12px;
        color: var(--cx-muted);
        white-space: nowrap;
        margin-top: 1px;
    }

    .ca-company-text {
        color: var(--cx-text);
        font-weight: 500;
    }

    /* Badges de Categoria */
    .ca-badge-cat {
        display: inline-flex;
        align-items: center;
        padding: 3px 9px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 600;
        line-height: 1.1;
    }

    .ca-badge-cat.cat-client {
        background: rgba(var(--success-500), 0.12);
        color: rgb(var(--success-400));
        border: 1px solid rgba(var(--success-500), 0.25);
    }
    .ca-badge-cat.cat-lead {
        background: rgba(var(--primary-500), 0.12);
        color: rgb(var(--primary-400));
        border: 1px solid rgba(var(--primary-500), 0.25);
    }
    .ca-badge-cat.cat-supplier {
        background: rgba(var(--purple-500), 0.12);
        color: rgb(var(--purple-400));
        border: 1px solid rgba(var(--purple-500), 0.25);
    }
    .ca-badge-cat.cat-partner {
        background: rgba(var(--warning-500), 0.12);
        color: rgb(var(--warning-400));
        border: 1px solid rgba(var(--warning-500), 0.25);
    }

    /* Badges de Status */
    .ca-badge-status {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 600;
        line-height: 1.2;
    }

    .ca-badge-status.status-active {
        background: rgba(var(--success-500), 0.12);
        color: rgb(var(--success-400));
    }
    .ca-badge-status.status-in_contact {
        background: rgba(var(--primary-500), 0.14);
        color: rgb(var(--primary-400));
    }
    .ca-badge-status.status-pending {
        background: rgba(var(--warning-500), 0.14);
        color: rgb(var(--warning-400));
    }
    .ca-badge-status.status-no_response {
        background: rgba(var(--danger-500), 0.14);
        color: rgb(var(--danger-400));
    }
    .ca-badge-status.status-in_negotiation {
        background: rgba(var(--primary-500), 0.14);
        color: rgb(var(--primary-400));
    }

    .ca-phone-mail {
        display: flex;
        flex-direction: column;
    }
    .ca-phone {
        color: var(--cx-text);
        font-weight: 500;
    }
    .ca-mail {
        color: var(--cx-subtle);
        font-size: 11px;
        margin-top: 1px;
    }

    .ca-date-time {
        display: flex;
        flex-direction: column;
    }
    .ca-date {
        color: var(--cx-text);
        font-weight: 500;
    }
    .ca-time {
        color: var(--cx-subtle);
        font-size: 11px;
        margin-top: 1px;
    }

    .ca-action-trigger {
        background: transparent;
        border: none;
        color: var(--cx-muted);
        padding: 4px 8px;
        border-radius: 6px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
    }

    .ca-action-trigger:hover {
        background: var(--cx-hover);
        color: var(--cx-text);
    }

    /* Coluna Lateral (Sidebar Widgets) */
    .ca-sidebar {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .ca-widget {
        background: var(--cx-surface);
        border: 1px solid var(--cx-border);
        border-radius: var(--cx-radius);
        padding: 18px 20px;
        box-shadow: var(--cx-shadow);
    }

    .ca-widget-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .ca-widget-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--cx-text);
        letter-spacing: -0.01em;
    }

    .ca-widget-link {
        font-size: 12px;
        color: rgb(var(--primary-400));
        font-weight: 500;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 4px;
        transition: color 0.15s ease;
    }

    .ca-widget-link:hover {
        color: rgb(var(--primary-300));
    }

    /* Mini Calendário */
    .ca-cal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
    }

    .ca-cal-month-title {
        font-size: 13px;
        font-weight: 600;
        color: var(--cx-text);
    }

    .ca-cal-nav {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .ca-cal-btn {
        width: 26px;
        height: 26px;
        border-radius: 6px;
        background: var(--cx-surface-2);
        border: 1px solid var(--cx-border);
        color: var(--cx-muted);
        display: grid;
        place-items: center;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .ca-cal-btn:hover {
        background: var(--cx-hover);
        color: var(--cx-text);
        border-color: var(--cx-border-strong);
    }

    .ca-cal-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
        text-align: center;
    }

    .ca-cal-weekday {
        font-size: 11px;
        font-weight: 500;
        color: var(--cx-subtle);
        padding-bottom: 8px;
    }

    .ca-cal-day {
        position: relative;
        height: 34px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 500;
        color: var(--cx-text);
        border-radius: 8px;
        cursor: pointer;
        background: transparent;
        border: none;
        transition: all 0.15s ease;
    }

    .ca-cal-day:hover {
        background: var(--cx-hover);
    }

    .ca-cal-day.is-out {
        color: var(--cx-subtle);
        opacity: 0.35;
    }

    .ca-cal-day.is-selected {
        background: rgb(var(--primary-600));
        color: #ffffff;
        font-weight: 700;
        border-radius: 9999px;
        width: 30px;
        height: 30px;
        margin: auto;
        box-shadow: 0 2px 8px rgba(var(--primary-600), 0.4);
    }

    .ca-cal-day-dots {
        position: absolute;
        bottom: 2px;
        display: flex;
        gap: 2px;
        align-items: center;
        justify-content: center;
    }

    .ca-cal-dot {
        width: 4px;
        height: 4px;
        border-radius: 9999px;
    }

    .ca-cal-legend {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 16px;
        padding-top: 12px;
        border-top: 1px solid var(--cx-border);
        font-size: 11px;
        color: var(--cx-muted);
    }

    .ca-legend-items {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .ca-legend-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .ca-legend-dot {
        width: 6px;
        height: 6px;
        border-radius: 9999px;
    }

    .ca-btn-today {
        padding: 2px 8px;
        border-radius: 4px;
        background: var(--cx-surface-2);
        border: 1px solid var(--cx-border);
        color: var(--cx-text);
        font-size: 11px;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .ca-btn-today:hover {
        background: var(--cx-hover);
    }

    /* Próximos Contatos */
    .ca-upcoming-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .ca-upcoming-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        background: var(--cx-surface-2);
        border: 1px solid var(--cx-border);
        border-radius: 10px;
        transition: all 0.15s ease;
    }

    .ca-upcoming-item:hover {
        background: var(--cx-hover);
        border-color: var(--cx-border-strong);
    }

    .ca-upcoming-time-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 58px;
        height: 44px;
        border-radius: 8px;
        background: var(--cx-surface);
        border: 1px solid var(--cx-border);
        flex-shrink: 0;
    }

    .ca-upcoming-time {
        font-size: 11px;
        font-weight: 700;
        color: var(--cx-text);
        line-height: 1.1;
    }

    .ca-upcoming-type-icon {
        width: 14px;
        height: 14px;
        margin-top: 3px;
    }

    .ca-upcoming-content {
        display: flex;
        flex-direction: column;
        min-width: 0;
        flex: 1;
    }

    .ca-upcoming-title {
        font-size: 13px;
        font-weight: 600;
        color: var(--cx-text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ca-upcoming-contact {
        font-size: 12px;
        color: var(--cx-muted);
        margin-top: 1px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Categorias */
    .ca-cat-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .ca-cat-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 12px;
        border-radius: 10px;
        background: var(--cx-surface-2);
        border: 1px solid var(--cx-border);
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .ca-cat-row:hover {
        background: var(--cx-hover);
        border-color: var(--cx-border-strong);
    }

    .ca-cat-row.is-active {
        border-color: rgb(var(--primary-500));
        background: rgba(var(--primary-500), 0.08);
    }

    .ca-cat-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .ca-cat-icon-badge {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: grid;
        place-items: center;
    }

    .ca-cat-name {
        font-size: 13px;
        font-weight: 500;
        color: var(--cx-text);
    }

    .ca-cat-right {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 600;
        color: var(--cx-muted);
    }

    .ca-cat-arrow {
        color: var(--cx-subtle);
        font-size: 12px;
    }

    /* Dropdown de Ações e Filtros */
    .ca-dropdown-menu {
        position: absolute;
        right: 0;
        top: 100%;
        margin-top: 6px;
        width: 180px;
        background: var(--cx-surface);
        border: 1px solid var(--cx-border-strong);
        border-radius: 10px;
        padding: 6px;
        box-shadow: var(--cx-shadow);
        z-index: 50;
    }

    .ca-dropdown-item {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        padding: 8px 10px;
        border-radius: 6px;
        font-size: 12px;
        color: var(--cx-text);
        background: transparent;
        border: none;
        text-align: left;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .ca-dropdown-item:hover {
        background: var(--cx-hover);
    }

    .ca-dropdown-item.is-danger {
        color: rgb(var(--danger-400));
    }

    .ca-dropdown-item.is-danger:hover {
        background: rgba(var(--danger-500), 0.12);
        color: rgb(var(--danger-300));
    }
</style>
