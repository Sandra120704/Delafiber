/**
 * Sistema de Control de Uso - Google Maps API
 * Previene exceder los límites gratuitos
 */

class MapaUsageController {
    constructor() {
        this.storageKey = 'mapa_usage_stats';
        this.dailyLimit = 800; // Límite diario conservador (28,500/30 días = 950, menos margen)
        this.usageStats = this.loadUsageStats();
        this.lastReloadTime = 0;
        this.minReloadInterval = 30000; // 30 segundos mínimo entre recargas
    }

    /**
     * Cargar estadísticas de uso desde localStorage
     */
    loadUsageStats() {
        try {
            const stored = localStorage.getItem(this.storageKey);
            if (stored) {
                const stats = JSON.parse(stored);
                const today = this.getTodayKey();
                
                // Si es un nuevo día, resetear contador
                if (!stats[today]) {
                    stats[today] = 0;
                    // Limpiar datos antiguos 
                    this.cleanOldStats(stats);
                }
                
                return stats;
            }
        } catch (error) {
            console.warn('Error cargando estadísticas de uso:', error);
        }
        
        // Inicializar si no existe
        const today = this.getTodayKey();
        return { [today]: 0 };
    }

    /**
     * Guardar estadísticas de uso
     */
    saveUsageStats() {
        try {
            localStorage.setItem(this.storageKey, JSON.stringify(this.usageStats));
        } catch (error) {
            console.warn('Error guardando estadísticas de uso:', error);
        }
    }

    /**
     * Obtener clave del día actual
     */
    getTodayKey() {
        return new Date().toISOString().split('T')[0]; // YYYY-MM-DD
    }

    /**
     * Limpiar estadísticas antiguas
     */
    cleanOldStats(stats) {
        const today = new Date();
        const sevenDaysAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
        
        Object.keys(stats).forEach(dateKey => {
            const statDate = new Date(dateKey);
            if (statDate < sevenDaysAgo) {
                delete stats[dateKey];
            }
        });
    }

    /**
     * Verificar si se puede realizar una operación
     */
    canPerformOperation(operationType = 'map_load') {
        const today = this.getTodayKey();
        const todayUsage = this.usageStats[today] || 0;

        // Verificar límite diario
        if (todayUsage >= this.dailyLimit) {
            this.showUsageLimitWarning(todayUsage);
            return false;
        }

        // Verificar intervalo mínimo para recargas
        if (operationType === 'reload') {
            const now = Date.now();
            if (now - this.lastReloadTime < this.minReloadInterval) {
                this.showReloadLimitWarning();
                return false;
            }
            this.lastReloadTime = now;
        }

        return true;
    }

    /**
     * Registrar uso de la API
     */
    recordUsage(operationType = 'map_load', count = 1) {
        const today = this.getTodayKey();
        
        if (!this.usageStats[today]) {
            this.usageStats[today] = 0;
        }
        
        this.usageStats[today] += count;
        this.saveUsageStats();
        
        // Log para monitoreo
        console.log(` Uso API registrado: ${operationType} (+${count}). Total hoy: ${this.usageStats[today]}/${this.dailyLimit}`);
        
        // Mostrar advertencias preventivas
        this.checkUsageWarnings();
    }

    /**
     * Verificar y mostrar advertencias de uso
     */
    checkUsageWarnings() {
        const today = this.getTodayKey();
        const todayUsage = this.usageStats[today] || 0;
        const percentage = (todayUsage / this.dailyLimit) * 100;

        if (percentage >= 80 && percentage < 90) {
            this.showUsageWarning('80% del límite diario usado', 'warning');
        } else if (percentage >= 90) {
            this.showUsageWarning('90% del límite diario usado - ¡Cuidado!', 'error');
        }
    }

    /**
     * Mostrar advertencia de límite de uso
     */
    showUsageLimitWarning(usage) {
        Swal.fire({
            icon: 'warning',
            title: '⚠️ Límite Diario Alcanzado',
            html: `
                <p>Has alcanzado el límite diario de uso del mapa (<strong>${usage}/${this.dailyLimit}</strong>).</p>
                <p>Esto es para proteger tu cuenta de Google Maps de cargos inesperados.</p>
                <hr>
                <small><strong>Soluciones:</strong></small>
                <ul style="text-align: left; font-size: 0.9em;">
                    <li>Espera hasta mañana (se resetea automáticamente)</li>
                    <li>Si es urgente, puedes aumentar el límite en la configuración</li>
                    <li>Revisa si hay recargas automáticas innecesarias</li>
                </ul>
            `,
            confirmButtonText: 'Entendido',
            footer: '<small>Este límite se resetea cada día a las 00:00</small>'
        });
    }

    /**
     * Mostrar advertencia de recarga frecuente
     */
    showReloadLimitWarning() {
        Swal.fire({
            icon: 'info',
            title: 'Recarga Muy Frecuente',
            text: `Por favor espera ${this.minReloadInterval/1000} segundos entre recargas para optimizar el uso de la API.`,
            timer: 3000,
            showConfirmButton: false
        });
    }

    /**
     * Mostrar advertencia general de uso
     */
    showUsageWarning(message, type = 'info') {
        const toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true
        });

        toast.fire({
            icon: type,
            title: message
        });
    }

    /**
     * Obtener estadísticas de uso actuales
     */
    getUsageStats() {
        const today = this.getTodayKey();
        const todayUsage = this.usageStats[today] || 0;
        
        return {
            today: todayUsage,
            limit: this.dailyLimit,
            percentage: Math.round((todayUsage / this.dailyLimit) * 100),
            remaining: this.dailyLimit - todayUsage,
            allStats: this.usageStats
        };
    }

    /**
     * Mostrar dashboard de uso
     */
    showUsageDashboard() {
        const stats = this.getUsageStats();
        
        Swal.fire({
            icon: 'info',
            title: ' Estadísticas de Uso del Mapa',
            html: `
                <div style="text-align: left;">
                    <h6> Uso de Hoy:</h6>
                    <div class="progress mb-3" style="height: 20px;">
                        <div class="progress-bar ${stats.percentage >= 90 ? 'bg-danger' : stats.percentage >= 80 ? 'bg-warning' : 'bg-success'}" 
                             role="progressbar" style="width: ${stats.percentage}%">
                            ${stats.today}/${stats.limit} (${stats.percentage}%)
                        </div>
                    </div>
                    
                    <p><strong>Restante hoy:</strong> ${stats.remaining} cargas</p>
                    <p><strong>Estado:</strong> 
                        <span class="badge ${stats.percentage >= 90 ? 'bg-danger' : stats.percentage >= 80 ? 'bg-warning' : 'bg-success'}">
                            ${stats.percentage >= 90 ? 'Crítico' : stats.percentage >= 80 ? 'Precaución' : 'Normal'}
                        </span>
                    </p>
                    
                    <hr>
                    <small><strong> Tip:</strong> El límite se resetea cada día a medianoche.</small>
                </div>
            `,
            confirmButtonText: 'Cerrar',
            width: 500
        });
    }
}

// Exportar controlador globalmente
window.MapaUsageController = MapaUsageController;