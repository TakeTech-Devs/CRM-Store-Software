@extends('layouts.dashboard')

@section('title', 'Update Log')

@section('content')
@php
    // Add one entry here each time a new version is released. This file
    // travels with every update package (it lives under resources/), so
    // every store ends up with the same changelog after applying an update.
    $changelog = [
        '1.0.0' => [
            'label' => 'Base Version',
            'date'  => null,
            'notes' => ['Initial release.'],
        ],
        '1.0.1' => [
            'label' => 'Version Update',
            'date'  => '2026-07-17',
            'notes' => [
                'Added the self-update mechanism: Check for Update / Apply Update page under My Store > Updates.',
                'Automatic 3-attempt retry on download/extract failure before surfacing an error.',
                'Full update history log recorded for every attempt.',
            ],
        ],
        '1.0.2' => [
            'label' => 'Version Update',
            'date'  => '2026-07-17',
            'notes' => [
                'Fixed inhouse product grouping in billing dropdowns — one product with multiple packs now shows correctly.',
                'Price auto-loads when only one option is available for a product/pack.',
                'Billing Brand dropdown now only shows brands with in-stock, non-expired products.',
            ],
        ],
        '1.0.3' => [
            'label' => 'Version Update',
            'date'  => '2026-07-17',
            'notes' => [
                'Unified bill preview columns (Brand + GST Rate) across customer/staff create and billing list pages.',
                'Customer Name/Mail and Doctor Mail are now optional in the Add modals, defaulting to the phone number when left blank.',
            ],
        ],
        '1.0.4' => [
            'label' => 'Version Update',
            'date'  => '2026-07-18',
            'notes' => [
                'Added the Update Log page (this page) with a per-version accordion and changelog notes.',
                'Fixed the sidebar Updates icon.',
            ],
        ],
        '1.0.5' => [
            'label' => 'Version Update',
            'date'  => '2026-07-18',
            'notes' => [
                'Customer/Staff Billing is now fully keyboard-operable: Phone -> Doctor -> Payment Type -> Brand -> Product -> Pack -> Price -> Qty auto-advances as you go.',
                'New shortcuts on the billing pages: Ctrl+Backspace deletes the current row, Ctrl+C/Ctrl+D (Ctrl+S/Ctrl+D on staff) open Add Customer/Doctor/Staff, Ctrl+Up/Down jumps between rows, Ctrl+Enter submits the bill, and P/R print the receipt while the print preview is open.',
                'Fixed a phantom horizontal scrollbar that could appear whenever a dropdown opened.',
                'Fixed dropdowns not closing properly behind popups, which could leave the wrong field focused.',
                'New global shortcuts: Alt+C and Alt+S jump straight to Create Customer/Staff Billing from anywhere in the app.',
                'Create Stock Transfer now uses the same Brand -> Product -> Pack -> Price selection style as billing, only showing brands currently in stock.',
            ],
        ],
        '1.0.6' => [
            'label' => 'Version Update',
            'date'  => '2026-08-15',
            'notes' => [
                'New Return system: customers and staff can return items from any of their last 3 bills within 30 days, generating a credit note that can later be applied toward a new bill of equal or greater value.',
                'New same-day bill correction: staff can reduce or remove items from a bill on the day it was created, which automatically restores the stock and adjusts the bill total. A bill that has been returned can no longer be edited this way, and a bill that has been edited this way can no longer be returned, to keep stock accurate.',
                'Fixed: doctors added at the store were not reaching the admin database during Sync Out — this now works correctly.',
                'Doctor Wise Report: selecting a doctor and generating the report now also shows a Patient Count card, a Bill Amount card, and a 6-month trend chart.',
                'Bills that were fully corrected down to zero no longer appear in the Sales Report, GST Report, Doctor Report, Analytics page, or the Dashboard\'s recent bills.',
                'Bill printing now correctly fits the 4x7 inch thermal receipt paper.',
                'The sidebar now highlights whichever section you are currently viewing.',
                'Self-update now also applies changes to the app\'s stylesheets, scripts, and images, not just backend code — and fails immediately with a clear message if the server is missing required zip support, instead of retrying silently.',
            ],
        ],
    ];
@endphp

<style>
    .version-timeline { list-style: none; padding-left: 0; margin: 0; }
    .version-timeline .v-item {
        border-left: 4px solid #28a745;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 10px;
        overflow: hidden;
    }
    .version-timeline .v-item.base { border-left-color: #6c757d; background: #eef0f2; }
    .version-timeline .v-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 18px;
        cursor: pointer;
        font-size: 1rem;
    }
    .version-timeline .v-item.base .v-header { cursor: default; font-weight: 700; }
    .version-timeline .v-label { font-weight: 600; }
    .version-timeline .v-done {
        background: #28a745;
        color: #fff;
        padding: 3px 12px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-right: 10px;
    }
    .version-timeline .v-date {
        color: #6c757d;
        font-size: 0.85rem;
    }
    .version-timeline .v-notes {
        padding: 0 18px 16px 42px;
        margin: 0;
        font-size: 0.95rem;
        color: #555;
    }
    .version-timeline .v-notes li { margin-bottom: 4px; }
    .version-timeline .v-notes li:last-child { margin-bottom: 0; }
    .version-timeline .v-header .fa-chevron-down { transition: transform .2s ease; }
    .version-timeline .v-header[aria-expanded="true"] .fa-chevron-down { transform: rotate(180deg); }
</style>

<div class="container-fluid">
    <div class="card mb-4">
        <div class="card-body text-center py-5">
            <h2 class="text-success mb-3"><i class="fas fa-check-circle"></i> Update Log</h2>
            <p class="mb-1">If you can see this page, the self-update mechanism successfully copied new files.</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">Version History</h5>
        </div>
        <div class="card-body">
            <ul class="version-timeline" id="versionAccordion">
                <li class="v-item base">
                    <div class="v-header">
                        <span><i class="fas fa-flag mr-2"></i>{{ $changelog['1.0.0']['label'] }}</span>
                        <span>1.0.0</span>
                    </div>
                </li>
                @foreach ($changelog as $version => $entry)
                    @continue($version === '1.0.0')
                    @php $itemId = str_replace('.', '-', $version); @endphp
                    <li class="v-item">
                        <div class="v-header" data-toggle="collapse" data-target="#notes-{{ $itemId }}"
                             aria-expanded="false" aria-controls="notes-{{ $itemId }}">
                            <span class="v-label"><i class="fas fa-code-branch mr-2 text-success"></i>{{ $entry['label'] }}: {{ $version }}</span>
                            <span>
                                @if (!empty($entry['date']))
                                    <span class="v-date mr-2"><i class="far fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($entry['date'])->format('d M Y') }}</span>
                                @endif
                                <span class="v-done">Done</span>
                                <i class="fas fa-chevron-down"></i>
                            </span>
                        </div>
                        <div class="collapse" id="notes-{{ $itemId }}" data-parent="#versionAccordion">
                            <ul class="v-notes">
                                @foreach ($entry['notes'] as $note)
                                    <li>{{ $note }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

<script>
    $(document).on('shown.bs.collapse hidden.bs.collapse', '.version-timeline .collapse', function () {
        $(this).prev('.v-header').attr('aria-expanded', $(this).hasClass('show'));
    });
</script>
@endsection
