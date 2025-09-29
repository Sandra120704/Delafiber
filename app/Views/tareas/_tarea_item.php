<div class="card mb-3 <?= $tarea['estado'] == 'Completada' ? 'border-success' : '' ?>">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
                <div class="d-flex align-items-center mb-2">
                    <?php if ($tarea['estado'] == 'Completada'): ?>
                        <i class="icon-check-circle text-success mr-2" style="font-size: 24px;"></i>
                    <?php else: ?>
                        <i class="icon-circle text-muted mr-2" style="font-size: 24px;"></i>
                    <?php endif; ?>
                    
                    <div class="flex-grow-1">
                        <h5 class="mb-0 <?= $tarea['estado'] == 'Completada' ? 'text-muted' : '' ?>">
                            <?= esc($tarea['titulo']) ?>
                        </h5>
                        <?php if ($tarea['lead_asociado']): ?>
                        <small class="text-muted">
                            <i class="icon-user"></i> <?= esc($tarea['lead_asociado']) ?>
                        </small>
                        <?php endif; ?>
                    </div>

                    <!-- Prioridad Badge -->
                    <?php 
                    $badgeClass = 'secondary';
                    if ($tarea['prioridad'] == 'Alta') $badgeClass = 'danger';
                    elseif ($tarea['prioridad'] == 'Media') $badgeClass = 'warning';
                    ?>
                    <span class="badge badge-<?= $badgeClass ?> ml-2">
                        <?= esc($tarea['prioridad']) ?>
                    </span>
                </div>

                <?php if ($tarea['descripcion']): ?>
                <p class="text-muted mb-2"><?= esc($tarea['descripcion']) ?></p>
                <?php endif; ?>

                <div class="d-flex align-items-center text-muted">
                    <small>
                        <i class="icon-calendar"></i> 
                        <?php 
                        $fechaVencimiento = strtotime($tarea['fecha_vencimiento']);
                        $hoy = strtotime(date('Y-m-d'));
                        $esHoy = date('Y-m-d', $fechaVencimiento) == date('Y-m-d');
                        $vencida = $fechaVencimiento < $hoy && $tarea['estado'] != 'Completada';
                        ?>
                        <span class="<?= $vencida ? 'text-danger font-weight-bold' : '' ?>">
                            <?php if ($esHoy): ?>
                                Hoy a las <?= date('H:i', $fechaVencimiento) ?>
                            <?php else: ?>
                                <?= date('d/m/Y H:i', $fechaVencimiento) ?>
                            <?php endif; ?>
                            <?php if ($vencida): ?>
                                (Vencida)
                            <?php endif; ?>
                        </span>
                    </small>
                    
                    <span class="mx-2">•</span>
                    
                    <small>
                        <span class="badge badge-<?= $tarea['estado'] == 'Completada' ? 'success' : ($tarea['estado'] == 'En Progreso' ? 'info' : 'secondary') ?>">
                            <?= esc($tarea['estado']) ?>
                        </span>
                    </small>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="btn-group ml-3">
                <?php if ($tarea['estado'] != 'Completada'): ?>
                <button type="button" 
                        class="btn btn-sm btn-success" 
                        onclick="completarTarea(<?= $tarea['idtarea'] ?>)"
                        title="Marcar como completada">
                    <i class="icon-check"></i>
                </button>
                <?php endif; ?>
                
                <button type="button" 
                        class="btn btn-sm btn-warning" 
                        onclick='editarTarea(<?= json_encode($tarea) ?>)'
                        title="Editar">
                    <i class="icon-pencil"></i>
                </button>
                
                <button type="button" 
                        class="btn btn-sm btn-danger" 
                        onclick="eliminarTarea(<?= $tarea['idtarea'] ?>)"
                        title="Eliminar">
                    <i class="icon-trash"></i>
                </button>
            </div>
        </div>
    </div>
</div>