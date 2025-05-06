<div class="permissions-tree" id="checkboxes-permisstions" data-name="foo" >
    @foreach ($children['root'] as $keyOne => $elOne)
        <ul class="parent_tree m-0 p-0 list-unstyled" id="node{{ $keyOne }}" >
            <li class="permissions-item list-unstyled">
                <div class="permissions-header">
                    <x-core::form.checkbox id="checkbox_one_{{ $keyOne }}" class="check-success" name="flags[]" value="{{ $flags[$elOne]['flag'] }}" checked="{{ in_array($flags[$elOne]['flag'], $active) }}" >
                        <x-slot:label>
                            <x-core::badge lite color="success" :label="$flags[$elOne]['name']" />
                        </x-slot:label>
                    </x-core::form.checkbox>
                </div>
            @if (isset($children[$elOne]))
                <ul class="row permissions-body {{ (isset($children[$elOne]) && count($children[$elOne]) > 0) ? 'has-children' : 'single-node' }}">
                    @foreach ($children[$elOne] as $keyTwo => $elTwo)
                        <li class="list-unstyled col-4 m-0" style="background-color: inherit" id="node_sub_{{ $keyOne }}_{{ $keyTwo }}" >
                            <x-core::form.checkbox id="checkbox_two_{{ $keyOne }}_{{ $keyTwo }}" name="flags[]" value="{{ $flags[$elTwo]['flag'] }}" checked="{{ in_array($flags[$elTwo]['flag'], $active) }}" >
                                <x-slot:label>
                                    <x-core::badge lite color="primary" :label="$flags[$elTwo]['name']" />
                                </x-slot:label>
                            </x-core::form.checkbox>
                            @if (isset($children[$elTwo]))
                                <ul class="list-unstyled">
                                    @foreach ($children[$elTwo] as $keyThree => $elThree)
                                        <li style="background-color: inherit" id="node_sub_sub_{{ $keyThree }}" >
                                            <x-core::form.checkbox id="checkbox_three_{{ $keyThree }}" class="check-yellow" name="flags[]" value="{{ $flags[$elThree]['flag'] }}" checked="{{ in_array($flags[$elThree]['flag'], $active) }}" >
                                                <x-slot:label class="small">
                                                    <x-core::badge lite color="yellow" :label="$flags[$elThree]['name']" />
                                                </x-slot:label>
                                            </x-core::form.checkbox>
                                            @if (isset($children[$elThree]))
                                                <ul class="list-unstyled">
                                                    @foreach ($children[$elThree] as $keyFour => $elFour)
                                                        <li style="background-color: inherit" id="node_grand_child{{ $keyFour }}" >
                                                            <x-core::form.checkbox id="checkbox_four_{{ $keyFour }}" name="flags[]" value="{{ $flags[$elFour]['flag'] }}" checked="{{ in_array($flags[$elFour]['flag'], $active) }}" >
                                                                <x-slot:label>
                                                                    <small>{{ $flags[ $elFour ]['name'] }}</small>
                                                                </x-slot:label>
                                                            </x-core::form.checkbox>
                                                            @if (isset($children[$elFour]))
                                                                <ul class="list-unstyled">
                                                                    @foreach ($children[$elFour] as $keyFive => $elFive)
                                                                        <li style="background-color: inherit" id="node{{ $grandChildrenKey }}" >
                                                                            <x-core::form.checkbox id="checkbox_five_{{ $keyFive }}" name="flags[]" value="{{ $flags[$elFour]['flag'] }}" checked="{{ in_array($flags[$elFour]['flag'], $active) }}" >
                                                                                <x-slot:label>{{ $flags[$elFour]['name'] }}</x-slot:label>
                                                                            </x-core::form.checkbox>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            </li>
                    @endforeach
                </ul>
            @endif
            </li>
        </ul>
    @endforeach
</div>
