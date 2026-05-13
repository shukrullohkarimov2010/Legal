{{--@extends('admin.layouts.app')--}}

{{--@section('content')--}}
{{--    <div class="container py-5" style="max-width: 720px;">--}}

{{--        <h1 class="fw-bold mb-4">Редактировать задачу</h1>--}}

{{--        <form method="POST"--}}
{{--              action="{{ route('tasks.update', $task) }}"--}}
{{--              class="card shadow-sm">--}}
{{--            @csrf--}}
{{--            @method('PUT')--}}

{{--            <div class="card-body">--}}

{{--                <div class="mb-3">--}}
{{--                    <label class="form-label">--}}
{{--                        Название <span class="text-danger">*</span>--}}
{{--                    </label>--}}
{{--                    <input type="text"--}}
{{--                           name="title"--}}
{{--                           value="{{ old('title', $task->title) }}"--}}
{{--                           class="form-control @error('title') is-invalid @enderror">--}}
{{--                    @error('title')--}}
{{--                    <div class="invalid-feedback">{{ $message }}</div>--}}
{{--                    @enderror--}}
{{--                </div>--}}

{{--                <div class="mb-3">--}}
{{--                    <label class="form-label">Описание</label>--}}
{{--                    <textarea name="description"--}}
{{--                              rows="4"--}}
{{--                              class="form-control">{{ old('description', $task->description) }}</textarea>--}}
{{--                </div>--}}

{{--                <div class="row mb-3">--}}
{{--                    <div class="col-md-6 mb-3 mb-md-0">--}}
{{--                        <label class="form-label">Срок выполнения</label>--}}
{{--                        <input type="date"--}}
{{--                               name="deadline"--}}
{{--                               value="{{ old('deadline', $task->deadline) }}"--}}
{{--                               class="form-control">--}}
{{--                    </div>--}}

{{--                    <div class="col-md-6">--}}
{{--                        <label class="form-label">Приоритет</label>--}}
{{--                        <select name="priority" class="form-select">--}}
{{--                            <option value="1" {{ old('priority', $task->priority) == 1 ? 'selected' : '' }}>--}}
{{--                                Высокий--}}
{{--                            </option>--}}
{{--                            <option value="2" {{ old('priority', $task->priority) == 2 ? 'selected' : '' }}>--}}
{{--                                Обычный--}}
{{--                            </option>--}}
{{--                            <option value="3" {{ old('priority', $task->priority) == 3 ? 'selected' : '' }}>--}}
{{--                                Низкий--}}
{{--                            </option>--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <div class="mb-4">--}}
{{--                    <label class="form-label">Статус</label>--}}
{{--                    <select name="status" class="form-select">--}}
{{--                        <option value="{{ \App\Models\Task::STATUS_FAILED }}"--}}
{{--                            {{ old('status', $task->status) == \App\Models\Task::STATUS_FAILED ? 'selected' : '' }}>--}}
{{--                            Новая--}}
{{--                        </option>--}}
{{--                        <option value="{{ \App\Models\Task::STATUS_PENDING }}"--}}
{{--                            {{ old('status', $task->status) == \App\Models\Task::STATUS_PENDING ? 'selected' : '' }}>--}}
{{--                            В процессе--}}
{{--                        </option>--}}
{{--                        <option value="{{ \App\Models\Task::STATUS_COMPLETED }}"--}}
{{--                            {{ old('status', $task->status) == \App\Models\Task::STATUS_COMPLETED ? 'selected' : '' }}>--}}
{{--                            Выполнено--}}
{{--                        </option>--}}
{{--                    </select>--}}
{{--                </div>--}}

{{--                <div class="d-flex justify-content-between">--}}
{{--                    <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">--}}
{{--                        Отмена--}}
{{--                    </a>--}}

{{--                    <button type="submit" class="btn btn-primary">--}}
{{--                        Сохранить--}}
{{--                    </button>--}}
{{--                </div>--}}

{{--            </div>--}}
{{--        </form>--}}
{{--    </div>--}}
{{--@endsection--}}

